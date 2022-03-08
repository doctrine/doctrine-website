const enableTab = function (element) {
  element.ariaSelected = true;
  element.classList.add('active');
  const id = element.attributes.getNamedItem('aria-controls').nodeValue;
  const referredElement = document.getElementById(id);
  referredElement.classList.add('show');
  referredElement.classList.add('active');
};

const disableTab = function (element) {
  const id = element.attributes.getNamedItem('aria-controls').nodeValue;
  const targetElement = document.getElementById(id);
  targetElement.classList.remove('show');
  targetElement.classList.remove('active');
  element.ariaSelected = false;
  element.classList.remove('active');
};

const selectTab = function (targetElement) {
  getAllTabElements().forEach(function (element) {
    disableTab(element);
  });
  enableTab(targetElement);
};

const getAllTabElements = function () {
  return document.querySelectorAll('.nav-tabs .nav-link');
};

export default function () {
  const tabElements = getAllTabElements();

  tabElements.forEach(function (tabElement) {
    tabElement.addEventListener('click', function (event) {
      selectTab(event.target);
    });
  });
}
