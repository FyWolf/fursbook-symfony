window.__isFetching = false;
window.__offset = 5;
let endContent = false;
const postsDiv = document.getElementById('postsDiv');
const el = document.querySelector(".endScroll")

// $(window).scroll(function () {
//    if($(window).scrollTop() + $(window).height()>= $(document).height()){

//      if(!window.__isFetching) {
//         getmoredata();
//         postsDiv.classList.add("spinner");
//         window.__offset += 5;
//      }
//    }

// })

function getmoredata() {
  window.__isFetching = true;
  $.post(
    window.location.pathname,
    { 'offset': window.__offset },
    function (response) {
      if(response.postsList){
        $('#postsDiv').append(response.postsList);
        postsDiv.classList.remove("spinner");
        window.__isFetching = false;
      }
      else {
        postsDiv.classList.remove("spinner");
        window.__isFetching = false;
        endContent = true;
      }
    },
  );
 }

 function isElementInViewport (el) {
let rect = el.getBoundingClientRect();
  return (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /* or $(window).height() */
    rect.right <= (window.innerWidth || document.documentElement.clientWidth) /* or $(window).width() */
  );
}

function onVisibilityChange(el, callback) {
let old_visible;
  return function () {
    let visible = isElementInViewport(el);
    if (visible != old_visible) {
      old_visible = visible;
      if (typeof callback == 'function') {
        callback();
      }
    }
  }
}
let handler = onVisibilityChange(el, function() {
  if(!window.__isFetching && !endContent) {
    getmoredata();
    postsDiv.classList.add("spinner");
    window.__offset += 5;
  }
});


// jQuery
$(window).on('DOMContentLoaded load resize scroll', handler);