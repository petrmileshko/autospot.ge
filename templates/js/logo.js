var i = 0;
var isMouseOver = false; // Флаг для отслеживания нахождения курсора мыши над элементом

function mask(a) {
  i++;
  if (i < a || (i >= a && isMouseOver)) { // Добавляем условие, чтобы эффект срабатывал при первом запуске или если курсор находится над элементом
    for (var j = 1; j <= 7; j++) {
      $("#logo-" + j).attr("style", "-webkit-mask:-webkit-gradient(radial, 17 17, " + i + ", 17 17, " + (i + 15) + ", from(rgb(0, 0, 0)), color-stop(0.5, rgba(0, 0, 0, 0.2)), to(rgb(0, 0, 0)));");
    }

    setTimeout(function () {
      mask(a);
    }, 20 - i);
  } else {
    i = 0;
  }
}

$(document).ready(function () {
  mask(138); // Вызываем функцию при загрузке страницы для автоматического срабатывания эффекта

  $("#logo, #logo-2, #logo-3, #logo-4, #logo-5, #logo-6, #logo-7").mouseenter(function () {
    isMouseOver = true; // Устанавливаем флаг в true при наведении курсора мыши
    mask(138);
  }).mouseleave(function () {
    isMouseOver = false; // Устанавливаем флаг в false при уходе курсора мыши
  });
});