var selectedButton = localStorage.getItem('selectedButton');
    
    // Восстановить состояние кнопок в зависимости от выбранной кнопки
    if (selectedButton === 'lari') {
        document.addEventListener("DOMContentLoaded", function() {
            $("#usdPriceBlock_f, #usdPriceBlock_y, #usdPriceBlock_l, #usdPriceBlock_m, #usdPriceBlock_s, #usdPriceBlock_r").hide();
            $("#lariPriceBlock_f, #lariPriceBlock_y, #lariPriceBlock_l, #lariPriceBlock_m, #lariPriceBlock_s, #lariPriceBlock_r").show();
        });
    } else {
        document.addEventListener("DOMContentLoaded", function() {
            $("#usdPriceBlock_f, #usdPriceBlock_y, #usdPriceBlock_l, #usdPriceBlock_m, #usdPriceBlock_s, #usdPriceBlock_r").show();
            $("#lariPriceBlock_f, #lariPriceBlock_y, #lariPriceBlock_l, #lariPriceBlock_m, #lariPriceBlock_s, #lariPriceBlock_r").hide();
        });
    }

    // При загрузке DOM
    document.addEventListener("DOMContentLoaded", function() {
        // При нажатии на кнопку "USD"
        $("#btnUSD, #btnUSD_f, #btnUSD_y, #btnUSD_l, #btnUSD_m, #btnUSD_s, #btnUSD_r").on("click", function() {
            // Показать блок с ценой в USD
            $("#usdPriceBlock_f, #usdPriceBlock_y, #usdPriceBlock_l, #usdPriceBlock_m, #usdPriceBlock_s, #usdPriceBlock_r").show();
            // Скрыть блок с ценой в LARI
            $("#lariPriceBlock_f, #lariPriceBlock_y, #lariPriceBlock_l, #lariPriceBlock_m, #lariPriceBlock_s, #lariPriceBlock_r").hide();
            // Сохранить выбранную кнопку в Local Storage
            localStorage.setItem('selectedButton', 'usd');
        });
        
        // При нажатии на кнопку "LARI"
        $("#btnLARI, #btnLARI_f, #btnLARI_y, #btnLARI_l, #btnLARI_m, #btnLARI_s, #btnLARI_r").on("click", function() {
            // Скрыть блок с ценой в USD
            $("#usdPriceBlock_f, #usdPriceBlock_y, #usdPriceBlock_l, #usdPriceBlock_m, #usdPriceBlock_s, #usdPriceBlock_r").hide();
            // Показать блок с ценой в LARI
            $("#lariPriceBlock_f, #lariPriceBlock_y, #lariPriceBlock_l, #lariPriceBlock_m, #lariPriceBlock_s, #lariPriceBlock_r").show();
            // Сохранить выбранную кнопку в Local Storage
            localStorage.setItem('selectedButton', 'lari');
        });
});