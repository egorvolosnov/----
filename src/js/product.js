// Смена основного изображения при клике на миниатюру
function changeImage(thumbnail, newSrc) {
    // Удаляем класс active у всех миниатюр
    document.querySelectorAll('.thumbnail').forEach(item => {
        item.classList.remove('active');
    });

    // Добавляем класс active к выбранной миниатюре
    thumbnail.classList.add('active');

    // Меняем основное изображение
    document.getElementById('mainImage').src = newSrc;
}

// Переключение табов
function openTab(evt, tabName) {
    // Скрываем все табы
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    // Убираем active у всех кнопок
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Показываем выбранный таб
    document.getElementById(tabName).classList.add('active');

    // Делаем кнопку активной
    evt.currentTarget.classList.add('active');
}