// Script for price range double-slider at index.html.twig

document.addEventListener('DOMContentLoaded', function () {
  const minVal = document.querySelector(".min-val");
  const maxVal = document.querySelector(".max-val");
  const priceInputMin = document.querySelector(".min-input");
  const priceInputMax = document.querySelector(".max-input");
  const minTooltip = document.querySelector(".min-tooltip");
  const maxTooltip = document.querySelector(".max-tooltip");
  const range = document.querySelector(".slider-track");
  
  const minGap = 30;
  const minLimit = parseInt(minVal.min);
  const maxLimit = parseInt(maxVal.max);

  if (!minVal || !maxVal || !priceInputMin || !priceInputMax || !range) return;

  function updateSlider() {
    const min = parseInt(minVal.value);
    const max = parseInt(maxVal.value);

    priceInputMin.value = min;
    priceInputMax.value = max;
    
    const percentMin = ((min - minLimit) / (maxLimit - minLimit)) * 100;
    const percentMax = ((max - minLimit) / (maxLimit - minLimit)) * 100;

    range.style.left = `${percentMin}%`;
    range.style.right = `${100 - percentMax}%`;

    minTooltip.style.left = `${percentMin}%`;
    maxTooltip.style.left = `${percentMax}%`;

    minTooltip.textContent = `$${min}`;
    maxTooltip.textContent = `$${max}`;
  }

  function slideMin() {
    if (parseInt(maxVal.value) - parseInt(minVal.value) < minGap) {
      minVal.value = parseInt(maxVal.value) - minGap;
    }
    updateSlider();
  }

  function slideMax() {
    if (parseInt(maxVal.value) - parseInt(minVal.value) < minGap) {
      maxVal.value = parseInt(minVal.value) + minGap;
    }
    updateSlider();
  }

  function setMinInput() {
    let value = parseInt(priceInputMin.value);
    if (value < minLimit) value = minLimit;
    if (value > parseInt(maxVal.value) - minGap) value = parseInt(maxVal.value) - minGap;
    minVal.value = value;
    updateSlider();
  }

  function setMaxInput() {
    let value = parseInt(priceInputMax.value);
    if (value > maxLimit) value = maxLimit;
    if (value < parseInt(minVal.value) + minGap) value = parseInt(minVal.value) + minGap;
    maxVal.value = value;
    updateSlider();
  }

  // Initial setup
  minVal.addEventListener('input', slideMin);
  maxVal.addEventListener('input', slideMax);
  priceInputMin.addEventListener('change', setMinInput);
  priceInputMax.addEventListener('change', setMaxInput);
  updateSlider();
});
