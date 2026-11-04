async function startGeneration() {
    const keyword = document.getElementById('targetKeyword').value.trim();
    if (!keyword) {
        alert('Please enter a target keyword.');
        return;
    }

    const loader = document.getElementById('loader');
    loader.classList.remove('d-none');
    document.getElementById('dashboardContent').classList.add('d-none');

    // Fun dynamic loading text to keep the user engaged
    const loaderText = loader.querySelector('p') || loader;
    const messages = [
        "Analyzing search intent & keyword volume...",
        "Structuring LSI keywords & content brief outline...",
        "Writing long-form SEO article HTML...",
        "Generating meta tags & landing page schema..."
    ];
    
    let msgIdx = 0;
    const interval = setInterval(() => {
        msgIdx = (msgIdx + 1) % messages.length;
        if (loaderText.tagName === 'P') loaderText.innerText = messages[msgIdx];
    }, 3000);

    try {
        const response = await fetch('api/generate.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ keyword })
        });

        const data = await response.json();
        clearInterval(interval);

        if (!response.ok) throw new Error(data.error || 'Generation failed');

        document.getElementById('briefId').value = data.brief_id;
        // Automatically update the keyword input if Gemini corrected it
        if (data.corrected_keyword) {
            document.getElementById('targetKeyword').value = data.corrected_keyword;
        }
        document.getElementById('seoTitle').value = data.title;
        document.getElementById('seoMeta').value = data.meta_description;
        document.getElementById('seoBody').value = data.body_html;

        loader.classList.add('d-none');
        document.getElementById('dashboardContent').classList.remove('d-none');

        triggerAudit();
    } catch (error) {
        clearInterval(interval);
        alert('Error: ' + error.message);
        loader.classList.add('d-none');
    }
}

function runClientAudit(title, meta, bodyHtml, keyword) {
    const results = [];
    const kw = keyword.toLowerCase();

    const titleValid = title.length >= 50 && title.length <= 60;
    results.push({ name: 'Title Length (50-60 chars)', pass: titleValid, detail: `${title.length} chars` });

    // Updated to industry-standard 120-160 characters
    const metaValid = meta.length >= 120 && meta.length <= 160;
    results.push({ name: 'Meta Desc (120-160 chars)', pass: metaValid, detail: `${meta.length} chars` });

    const titleHasKw = title.toLowerCase().includes(kw);
    results.push({ name: 'Keyword in Title', pass: titleHasKw, detail: titleHasKw ? 'Found' : 'Missing' });

    const h2Matches = bodyHtml.match(/<h2[^>]*>(.*?)<\/h2>/gi) || [];
    const h2Text = h2Matches.join(' ').toLowerCase();
    const h2HasKw = h2Text.includes(kw);
    results.push({ name: 'Keyword in H2 Heading', pass: h2HasKw, detail: h2HasKw ? 'Found' : 'Missing' });

    return results;
}

function triggerAudit() {
    const title = document.getElementById('seoTitle').value;
    const meta = document.getElementById('seoMeta').value;
    const body = document.getElementById('seoBody').value;
    const keyword = document.getElementById('targetKeyword').value;

    const checks = runClientAudit(title, meta, body, keyword);
    const ul = document.getElementById('checklistUl');
    ul.innerHTML = '';
    let passedCount = 0;

    checks.forEach(c => {
        if (c.pass) passedCount++;
        ul.innerHTML += `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><i class="bi ${c.pass ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger'} me-2"></i>${c.name}</span>
                <small class="text-muted">${c.detail}</small>
            </li>
        `;
    });

    const badge = document.getElementById('auditBadge');
    if (passedCount === checks.length) {
        badge.className = 'badge bg-success';
        badge.innerText = 'Passed (100%)';
    } else {
        badge.className = 'badge bg-warning text-dark';
        badge.innerText = `${passedCount}/${checks.length} Passed`;
    }
}

async function saveToDatabase() {
    const payload = {
        brief_id: document.getElementById('briefId').value,
        target_keyword: document.getElementById('targetKeyword').value,
        title: document.getElementById('seoTitle').value,
        meta_description: document.getElementById('seoMeta').value,
        body_html: document.getElementById('seoBody').value,
        content_type: 'blog'
    };

    try {
        const response = await fetch('api/save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const data = await response.json();
        if (!response.ok) throw new Error(data.error || 'Save failed');

        alert(data.message + ' Slug: /' + data.slug);
    } catch (error) {
        alert('Save Error: ' + error.message);
    }
}