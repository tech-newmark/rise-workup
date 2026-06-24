const reviews = document.querySelectorAll(".review-card");

function updateButtons() {
  reviews.forEach((review) => {
    const reviewText = review.querySelector(".review-card__text");
    const button = review.querySelector(".clear-btn");

    if (
      reviewText &&
      button &&
      reviewText.clientHeight < reviewText.scrollHeight
    ) {
      button.classList.add("active");
    } else {
      button.classList.remove("active");
    }
  });
}

window.addEventListener("resize", updateButtons);

BX.ready(updateButtons);
