// Search
const searchForm = document.querySelector("#search-button");
const searchBox = document.querySelector(".kotak-search");

document.querySelector('#search-button').onclick = (e) => {
  searchBox.classList.toggle('active');
  searchBox.focus();
  e.preventDefault();
}

// shopping cart
const shoppingCart = document.querySelector('.shopping-cart');
document.querySelector('#shopping-cart-button').onclick = (e) => {
  shoppingCart.classList.toggle('active');
  e.preventDefault();
}

const sb = document.querySelector('#search-button');
const sc = document.querySelector('#shopping-cart-button');

document.addEventListener('click', function (e) {
  if (!sb.contains(e.target) && !searchForm.contains(e.target)) {
    searchForm.classList.remove('active');
  }

  if (!sc.contains(e.target) && !shoppingCart.contains(e.target)) {
    shoppingCart.classList.remove('active');
  }
});

// Modal 1
const itemDetailModal = document.querySelector("#item-detail-modal");
const itemDetailButton = document.querySelectorAll(".item-detail-button");
// loop through each item-detail-button-2 and attach a click event listener
itemDetailButton.forEach(button => {
  button.onclick = (e) => {
    itemDetailModal.style.display = "flex";
    e.preventDefault();
  };
});
// close with icon
document.querySelector(".modals .close-icon").onclick = (e) => {
  itemDetailModal.style.display = "none";
  e.preventDefault();
};

// close outside the box 
const modals = document.querySelectorAll("#item-detail-modal, #item-detail-modal-2, #item-detail-modal-3, #item-detail-modal-4, #item-detail-modal-5");
window.onclick = (e) => {
  modals.forEach((modal) => {
    if (e.target === modal) {
      modal.style.display = "none";
    }
  });
};

// Modal 2
const itemDetailModal1 = document.querySelector("#item-detail-modal-2");
const itemDetailButton1 = document.querySelectorAll(".item-detail-button-2");
// loop through each item-detail-button-2 and attach a click event listener
itemDetailButton1.forEach(button => {
  button.onclick = (e) => {
    itemDetailModal1.style.display = "flex";
    e.preventDefault();
  };
});
// close with icon
document.querySelector(".modals .close-icon1").onclick = (e) => {
  itemDetailModal1.style.display = "none";
  e.preventDefault();
};

// Modal 3
const itemDetailModal2 = document.querySelector("#item-detail-modal-3");
const itemDetailButton2 = document.querySelectorAll(".item-detail-button-3");
// loop through each item-detail-button-2 and attach a click event listener
itemDetailButton2.forEach(button => {
  button.onclick = (e) => {
    itemDetailModal2.style.display = "flex";
    e.preventDefault();
  };
});
// close with icon
document.querySelector(".modals .close-icon2").onclick = (e) => {
  itemDetailModal2.style.display = "none";
  e.preventDefault();
};


// Modal 4
const itemDetailModal3 = document.querySelector("#item-detail-modal-4");
const itemDetailButton3 = document.querySelectorAll(".item-detail-button-4");
// loop through each item-detail-button-2 and attach a click event listener
itemDetailButton3.forEach(button => {
  button.onclick = (e) => {
    itemDetailModal3.style.display = "flex";
    e.preventDefault();
  };
});
// close with icon
document.querySelector(".modals .close-icon3").onclick = (e) => {
  itemDetailModal3.style.display = "none";
  e.preventDefault();
};

//modal 5
const itemDetailModal4 = document.querySelector("#item-detail-modal-5");
const itemDetailButton4 = document.querySelectorAll(".item-detail-button-5");
// loop through each item-detail-button-2 and attach a click event listener
itemDetailButton4.forEach(button => {
  button.onclick = (e) => {
    itemDetailModal4.style.display = "flex";
    e.preventDefault();
  };
});
// close with icon
document.querySelector(".modals .close-icon4").onclick = (e) => {
  itemDetailModal4.style.display = "none";
  e.preventDefault();
};


