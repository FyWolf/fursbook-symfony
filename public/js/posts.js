const image1 = document.querySelector('#new_post_image1');
const image2 = document.querySelector('#new_post_image2');
const image3 = document.querySelector('#new_post_image3');
const image4 = document.querySelector('#new_post_image4');

image2.disabled = true;
image3.disabled = true;
image4.disabled = true;

image1.addEventListener('change', (e) => {
    image2.disabled = false;
})

image2.addEventListener('change', (e) => {
    image3.disabled = false;
})

image3.addEventListener('change', (e) => {
    image4.disabled = false;
})
