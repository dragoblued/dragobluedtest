let center = [54.689383, 25.270894];
let addr = document.querySelector('#address') ?? document.querySelector('input[name="address"]');
let address_building_name = document.querySelector('input[name="address_building_name"]');
let locationUrl =  document.querySelector('input[name="address_url"]') ?? document.querySelector('input[name="location_url"]');
let address_coordinates =  document.querySelector('input[name="address_coordinates"]') ??  document.querySelector('input[name="location_coordinates"]');

const setMapVars = (newCenter) => {
   if (newCenter) {
      center = newCenter;
   }
}

ymaps.ready(init);
function init() {
   const myMap = new ymaps.Map('map', {
      center: center,
      zoom: 14
   }, {
      searchControlProvider: 'yandex#search'
   });
   let myPlacemark = createPlacemark(center);
   getAddress(center);

   const updateInputs = () => {
      const coordinates = myPlacemark.geometry.getCoordinates();
      const reverseCoordinates = `${myPlacemark.geometry.getCoordinates()[1]},${myPlacemark.geometry.getCoordinates()[0]}`;
      const zoom = myMap.getZoom();
      if (addr) {
         addr.value = myPlacemark.properties._data.balloonContent ?? 'Can\'t pick the place. Please, pick again';
      }
      if (address_building_name) {
         address_building_name.value = myPlacemark.properties._data.premiseName ?? 'Can\'t pick the place. Please, pick again';
      }
      if (address_coordinates) {
         console.log(coordinates);
         address_coordinates.value = JSON.stringify(coordinates);
      }
      if (locationUrl) {
         locationUrl.value = `https://yandex.ru/maps/?whatshere[point]=${reverseCoordinates}&whatshere[zoom]=${zoom}`;
      }
   }

   myMap.events.add('click', onMapClick);
   myMap.events.add('balloonopen', onMapBaloonOpen);

   function onMapBaloonOpen(event) {
      const coords = myMap.balloon.getData().properties.get('point');
      const premiseName = myMap.balloon.getData().properties.get('name');
      if (coords) {
         const temp = coords[0];
         coords[0] = coords[1];
         coords[1] = temp;
         // Если метка уже создана – просто передвигаем ее.
         if (myPlacemark) {
            myPlacemark.geometry.setCoordinates(coords);
         }
         // Если нет – создаем.
         else {
            myPlacemark = createPlacemark(coords);
         }
         getAddress(coords, premiseName, updateInputs);
      }
   }

   function onMapClick(event) {
      const coords = event.get('coords');

      // Если метка уже создана – просто передвигаем ее.
      if (myPlacemark) {
         myPlacemark.geometry.setCoordinates(coords);
      }
      // Если нет – создаем.
      else {
         myPlacemark = createPlacemark(coords);
      }

      getAddress(coords, null, updateInputs);
   }

   // Создание метки.
   function createPlacemark(coords) {
      const newPlacemark = new ymaps.Placemark(coords, {
         iconCaption: 'Search...'
      }, {
         preset: 'islands#violetDotIconWithCaption',
         draggable: true
      });
      myMap.geoObjects.add(newPlacemark);
      // Слушаем событие окончания перетаскивания на метке.
      newPlacemark.events.add('dragend', function() {
         getAddress(newPlacemark.geometry.getCoordinates(), null, updateInputs);
      });
      return newPlacemark;
   }

   // Определяем адрес по координатам (обратное геокодирование).
   function getAddress(coords, premiseName = null, callback = () => {}) {
      myPlacemark.properties.set('iconCaption', 'search...');
      ymaps.geocode(coords).then(function(res) {
         const firstGeoObject = res.geoObjects.get(0);

         myPlacemark.properties
            .set({
               // Формируем строку с данными об объекте.
               iconCaption: [
                  // Название населенного пункта или вышестоящее административно-территориальное образование.
                  firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                  // Получаем путь до топонима, если метод вернул null, запрашиваем наименование здания.
                  premiseName || firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
               ].filter(Boolean).join(', '),
               // В качестве контента балуна задаем строку с адресом объекта.
               balloonContent: firstGeoObject.getAddressLine(),
               premiseName: premiseName || firstGeoObject.getPremise() || firstGeoObject.getPremiseNumber()
            });

         callback();
      });
   }
}
window.setMapVars = setMapVars;
