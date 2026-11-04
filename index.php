<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FullStackPartner.com - AI SEO Content Engine</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">
                <i class="bi bi-cpu text-primary me-2"></i>FullStackPartner SEO Automation
            </span>
        </div>
    </nav>

    <div class="container my-5">
        <!-- Input Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <h4 class="card-title mb-3">Generate New SEO Content Asset</h4>
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label for="targetKeyword" class="form-label fw-bold">Target Keyword</label>
                        <input type="text" id="targetKeyword" class="form-control form-control-lg" placeholder="e.g., AI Marketing Automation for Agencies">
                    </div>
                    <div class="col-md-4">
                        <button id="generateBtn" class="btn btn-primary btn-lg w-100" onclick="startGeneration()">
                            <i class="bi bi-magic me-2"></i>Generate Brief & Content
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loader" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted mt-3">AI is analyzing search intent, generating brief, and writing content...</p>
        </div>

        <!-- Dashboard Content Area -->
        <div id="dashboardContent" class="d-none">
            <input type="hidden" id="briefId" value="">
            <div class="row">
                <!-- Main Editor Column -->
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0">Content Review & Editor</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="seoTitle" class="form-label fw-bold">SEO Title Tag</label>
                                <input type="text" id="seoTitle" class="form-control" oninput="triggerAudit()">
                            </div>
                            <div class="mb-3">
                                <label for="seoMeta" class="form-label fw-bold">Meta Description</label>
                                <textarea id="seoMeta" class="form-control" rows="2" oninput="triggerAudit()"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="seoBody" class="form-label fw-bold">Article Body (HTML)</label>
                                <textarea id="seoBody" class="form-control font-monospace" rows="12" oninput="triggerAudit()"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quality Checklist Sidebar -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">SEO Audit Checklist</h5>
                            <span id="auditBadge" class="badge bg-secondary">Pending</span>
                        </div>
                        <div class="card-body p-0">
                            <ul id="checklistUl" class="list-group list-group-flush"></ul>
                        </div>
                        <div class="card-footer bg-white p-3">
                            <button class="btn btn-success w-100" onclick="saveToDatabase()">
                                <i class="bi bi-save me-2"></i>Save & Publish to MySQL
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Application Script -->
    <script src="assets/js/app.js"></script>
</body>
</html>