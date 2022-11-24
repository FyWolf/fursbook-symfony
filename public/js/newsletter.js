window.__isFetching = false;
window.__offset = 5;
let endContent = false;
const newsDiv = document.getElementById('contentdiv');
const el = document.querySelector(".endScroll")
let oppenedDD = ''

function getmoredata() {
  window.__isFetching = true;
  $.post(
    window.location.pathname,
    {
      'offset': window.__offset,
      'action': 'scroll',
    },
    function (response) {
      if(response.newsList){
        $('#contentdiv').append(response.newsList);
        newsDiv.classList.remove("spinner");
        window.__isFetching = false;
      }
      else {
        newsDiv.classList.remove("spinner");
        window.__isFetching = false;
        endContent = true;
      }
    },
  );
}

let observer = new IntersectionObserver(function(entries) {
	if(entries[0].isIntersecting === true)
    if(!window.__isFetching && !endContent) {
      getmoredata();
      newsDiv.classList.add("spinner");
      window.__offset += 5;
    }
}, { threshold: [1] });

observer.observe(document.querySelector(".endScroll"));