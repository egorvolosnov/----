function initMap() {
        if (typeof ymaps === 'undefined') {
            console.error('Yandex Maps API не загрузилось');
            document.getElementById('map').innerHTML = 
                '<div style="height:100%;display:flex;align-items:center;justify-content:center;background:var(--card-bg);">'+
                '<p>Карта временно недоступна</p></div>';
            return;
        }

        const map = new ymaps.Map('map', {
            center: [55.76, 37.64],
            zoom: 5,
            controls: ['zoomControl']
        });

        // Магазины
        const stores = [
            {
                coords: [55.753215, 37.622504],
                name: "Москва",
                address: "ул. Техническая, 15"
            },
            {
                coords: [59.934280, 30.335099],
                name: "Санкт-Петербург", 
                address: "пр. Науки, 42"
            },
            {
                coords: [56.838011, 60.597465],
                name: "Екатеринбург",
                address: "ул. Мамина-Сибиряка, 132"
            }
        ];

        // Создаем метки
        stores.forEach(store => {
            const placemark = new ymaps.Placemark(
                store.coords, 
                {
                    balloonContent: `<strong>${store.name}</strong><br>${store.address}`
                },
                {
                    preset: 'islands#orangeIcon'
                }
            );
            map.geoObjects.add(placemark);
        });

        // Автоматически подгоняем карту
        map.setBounds(map.geoObjects.getBounds(), {
            checkZoomRange: true
        });
    }

    function loadYandexMaps() {
        const script = document.createElement('script');
        script.src = 'https://api-maps.yandex.ru/2.1/?apikey=ваш_api_ключ&lang=ru_RU&onload=initMap';
        script.async = true;
        document.head.appendChild(script);
    }

    // Загружаем API после полной загрузки страницы
    window.addEventListener('load', loadYandexMaps);