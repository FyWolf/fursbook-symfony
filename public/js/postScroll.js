window.__isFetching = false;
window.__offset = 5;
const postsDiv = document.getElementById('postsDiv');

$(window).scroll(function () {
   if($(window).scrollTop() + $(window).height()>= $(document).height()){

     if(!window.__isFetching) {
        getmoredata();
        postsDiv.classList.add("spinner");
        window.__offset += 5;
     }
   }

})

function getmoredata() {
  window.__isFetching = true;
  $.post(
    window.location.pathname,
    { 'offset': window.__offset },
    function (response) {
      $('#postsDiv').append(response.classifiedList);
      postsDiv.classList.remove("spinner");
      window.__isFetching = false;
    },
  );
 }