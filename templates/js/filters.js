var selectedButton = localStorage.getItem('selectedButton');

if (selectedButton === 'lari') {
    $("#usdPriceBlock_f, #usdPriceBlock_y, #usdPriceBlock_l, #usdPriceBlock_m, #usdPriceBlock_s, #usdPriceBlock_r").hide();
    $("#lariPriceBlock_f, #lariPriceBlock_y, #lariPriceBlock_l, #lariPriceBlock_m, #lariPriceBlock_s, #lariPriceBlock_r").show();
    $("#btnLARI, #btnLARI_f, #btnLARI_y, #btnLARI_l, #btnLARI_m, #btnLARI_s, #btnLARI_r").addClass('selected-button');
} else {
    
    $("#usdPriceBlock_f, #usdPriceBlock_y, #usdPriceBlock_l, #usdPriceBlock_m, #usdPriceBlock_s, #usdPriceBlock_r").show();
    $("#lariPriceBlock_f, #lariPriceBlock_y, #lariPriceBlock_l, #lariPriceBlock_m, #lariPriceBlock_s, #lariPriceBlock_r").hide();
    $("#btnUSD, #btnUSD_f, #btnUSD_y, #btnUSD_l, #btnUSD_m, #btnUSD_s, #btnUSD_r").addClass('selected-button');
}
document.addEventListener("DOMContentLoaded", function() {
    $("#btnUSD, #btnUSD_f, #btnUSD_y, #btnUSD_l, #btnUSD_m, #btnUSD_s, #btnUSD_r").on("click", function() {
        $(this).addClass('selected-button');
        $("#btnLARI, #btnLARI_f, #btnLARI_y, #btnLARI_l, #btnLARI_m, #btnLARI_s, #btnLARI_r").removeClass('selected-button');
    });

    $("#btnLARI, #btnLARI_f, #btnLARI_y, #btnLARI_l, #btnLARI_m, #btnLARI_s, #btnLARI_r").on("click", function() {

        $(this).addClass('selected-button');
        $("#btnUSD, #btnUSD_f, #btnUSD_y, #btnUSD_l, #btnUSD_m, #btnUSD_s, #btnUSD_r").removeClass('selected-button');
    });
});