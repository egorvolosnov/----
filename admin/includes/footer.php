        </div> <!-- закрываем admin-content -->
    </div> <!-- закрываем container -->
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Инициализация всех необходимых скриптов
document.addEventListener('DOMContentLoaded', function() {
    // Подтверждение удаления
    document.querySelectorAll('.confirm-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Вы уверены, что хотите выполнить это действие?')) {
                e.preventDefault();
            }
        });
    });
    
    // Инициализация подсказок
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
</body>
</html>