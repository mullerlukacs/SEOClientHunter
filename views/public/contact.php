<?php
$pageTitle = "Contact Support & Inquiries | SEO Client Hunter";
$pageMetaDescription = "Get in touch with the SEO Client Hunter customer success and technical support team.";

$cms = new App\CMS();
$page = $cms->getPageBySlug('contact');
$submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = true;
}

require VIEWS_DIR . '/layout/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-saas p-4 p-md-5">
                <div class="row g-5">
                    <div class="col-lg-6">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-2">Support & Agency Success</span>
                        <h2 class="fw-bold text-dark mb-3">Get In Touch With Our Team</h2>
                        <p class="text-secondary mb-4">Have questions about setting up custom search API keys, crawler behavior, or enterprise agency subscriptions? We are here to help.</p>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="p-3 bg-light rounded text-primary fs-5">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Support Email</small>
                                <span class="fw-bold text-dark">support@seoclienthunter.com</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="p-3 bg-light rounded text-primary fs-5">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Toll-Free Phone</small>
                                <span class="fw-bold text-dark">+1 (800) 555-SEOHUNT</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-light rounded text-primary fs-5">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Support Hours</small>
                                <span class="fw-bold text-dark">Monday - Friday, 9:00 AM - 6:00 PM EST</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="p-4 bg-light rounded border">
                            <?php if ($submitted): ?>
                                <div class="alert alert-success">
                                    <i class="fa-solid fa-circle-check me-2"></i> Thank you! Your message has been received. Our agency success team will respond within 24 hours.
                                </div>
                            <?php endif; ?>

                            <h5 class="fw-bold text-dark mb-3">Send Us A Message</h5>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Full Name</label>
                                    <input type="text" name="name" class="form-control" required placeholder="Alexander Wright">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Work Email</label>
                                    <input type="email" name="email" class="form-control" required placeholder="alex@myagency.com">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Subject</label>
                                    <input type="text" name="subject" class="form-control" required placeholder="Question about Pro Agency plan">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Message</label>
                                    <textarea name="message" class="form-control" rows="4" required placeholder="How can we assist you?"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Send Inquiry
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require VIEWS_DIR . '/layout/footer.php'; ?>
