        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/app.js"></script>
<script>
    const toggleAdminBtn = document.getElementById('toggleAdminSidebarBtn');
    const adminSidebar = document.getElementById('adminSidebar');
    if (toggleAdminBtn && adminSidebar) {
        toggleAdminBtn.addEventListener('click', () => {
            adminSidebar.classList.toggle('show');
        });
    }
</script>
</body>
</html>
