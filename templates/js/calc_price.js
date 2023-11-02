// Получение элементов HTML
var slider = document.querySelector(".price-range-slider");
var bar = document.querySelector(".price-range-bar");

// Минимальная и максимальная цены
var minPrice = <?php echo $min_price; ?>;
var maxPrice = <?php echo $max_price; ?>;

// Текущая цена
var currentPrice = <?php echo $data["ad"]["ads_price_usd"]; ?>;

// Вычисление положения бегунка на шкале
var position = ((currentPrice - minPrice) / (maxPrice - minPrice)) * 100;

// Установка начальной позиции бегунка
slider.style.left = position + "%";

// Флаг, определяющий, можно ли перемещать бегунок
var isSliderMovable = false;

// Обработчик события для перемещения бегунка
slider.addEventListener("mousedown", function(event) {
	if (!isSliderMovable) {
		return; // Не перемещать бегунок
	}
	
	var startX = event.clientX;
	var sliderX = slider.getBoundingClientRect().left;
	var barWidth = bar.offsetWidth;
	
	document.addEventListener("mousemove", onMouseMove);
	document.addEventListener("mouseup", onMouseUp);
	
	function onMouseMove(event) {
		var newX = event.clientX;
		var offsetX = newX - startX;
		var newPosition = ((sliderX - bar.getBoundingClientRect().left + offsetX) / barWidth) * 100;
		
		if (newPosition < 0) {
			newPosition = 0;
            } else if (newPosition > 100) {
			newPosition = 100;
		}
		
		slider.style.left = newPosition + "%";
	}
	
	function onMouseUp() {
		document.removeEventListener("mousemove", onMouseMove);
		document.removeEventListener("mouseup", onMouseUp);
	}
});

// Для блокировки перемещения бегунка, установите isSliderMovable в false
// Например, если вы хотите заблокировать бегунок при определенных условиях:
// if (условие) {
//     isSliderMovable = false;
// } else {
//     isSliderMovable = true;
// }