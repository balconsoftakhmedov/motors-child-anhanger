document.addEventListener('DOMContentLoaded', function () {
  setTimeout(() => {
    let priceAll = document.querySelectorAll('.price_wr .price .amount');
    priceAll.forEach((elem) => {
      if(elem.innerText.indexOf('0,00') == 0) {
        elem.parentElement.classList.add('remove');
      }
    })
    // console.log(priceAll);
  },1000)


  setTimeout(() => {
    let priceAll = document.querySelectorAll('.stm_rent_prices .price_item .amount');
    priceAll.forEach((elem) => {
      if(elem.innerText.indexOf('0,00') == 0) {
        elem.parentElement.classList.add('remove');
      }
    })
    // console.log(priceAll);
  },1000)

  
});

