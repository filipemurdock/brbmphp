const filterServicesInput = document.getElementById('filterServicesInput');
if (filterServicesInput) {
    const serviceTitle = document.querySelectorAll('.g-sitem');
    const serviceHeads = document.querySelectorAll('.g-category > .card-header');
    const nothingFound = document.querySelector('.nothing-found');
    const searchTextWrite = document.getElementById('search-text-write');

    filterServicesInput.addEventListener('keyup', e => {
        const keyword = e.target.value;
        $('.g-sitem').each(function () {
            var text = $(this).text().toLowerCase();
            if (text.indexOf(e.target.value.toLowerCase()) == -1) {
                $(this).addClass('hidden');
            } else {
                $(this).removeClass('hidden');
            }
        });

        const catCards = document.querySelectorAll('.g-category');
        [...catCards].forEach(card => {
            const itemsHidden = card.querySelectorAll('.g-sitem.hidden');
            const items = card.querySelectorAll('.g-sitem');
            if (itemsHidden.length == items.length) {
                card.style.display = 'none';
                card.classList.add('empty');
            } else {
                card.style.display = '';
                card.classList.remove('empty');
            }
        })

        const catCardsCount = catCards.length;
        const emptyCards = document.querySelectorAll('.g-category.empty');
        console.log(emptyCards.length, catCardsCount);
        if (emptyCards.length == catCardsCount) {
            nothingFound.style.display = '';
            searchTextWrite.innerHTML = keyword;
        } else {
            nothingFound.style.display = 'none';
            searchTextWrite.innerHTML = '';
        }
    });
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function noAuthMenu() {
		$('.b-menu-wrapper').toggleClass('active');
		$('body').toggleClass('stop-body');
}

var modalOpen = (modalId, data = null) => {
  const modal = document.getElementById(modalId);
  const modalBox = modal.querySelector('.modal-box');
  modal.classList.add('active');
  document.body.style.overflow = 'hidden';

  const closeModal = () => {
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
  }

  modal.addEventListener('click', e => {
    if (e.target !== modalBox && !modalBox.contains(e.target)) {
      closeModal();
    }
  });

  const modalCloseBtn = modal.querySelector('.m-close');
  if (modalCloseBtn) {
    modalCloseBtn.addEventListener('click', e => {
      closeModal();
    })
  }

  if (data != null) {
    Object.keys(data).forEach(key => {
      const el = document.getElementById(key);
      if (el) {
        el.innerHTML = data[key];
      }
    });
  }

}

const gdash = document.querySelector(".g-dash");
var authMenuToggle = e => {
    gdash.classList.toggle("g-bar")
};

if (!localStorage.gender) {
	localStorage.gender = 'male';
}

var acpaSwitch = document.querySelector('.g-switch');

const genderControl = (first = false) => {
  	
  
    var allAvatars = document.querySelectorAll('.user-avatar');
	if (localStorage.gender === 'female') {
        [...allAvatars].forEach(el => {
        	el.src = '../assets/osweld/img/female.png';
        });
      	if (acpaSwitch) {
        	acpaSwitch.classList.remove('gender-male');
	        acpaSwitch.classList.add('gender-female');
        }
	} else {
		[...allAvatars].forEach(el => {
        	el.src = '../assets/osweld/img/male.png';
        })
      	if (acpaSwitch) {
        	acpaSwitch.classList.remove('gender-female');
	        acpaSwitch.classList.add('gender-male');
        }
	}
}

if (acpaSwitch) {
  acpaSwitch.addEventListener('click', _ => {
      if (localStorage.gender === 'female') {
          localStorage.gender = 'male';
      } else {
          localStorage.gender = 'female'
      }
      genderControl();
  });
}
genderControl(true);


function setCookie(e, t, s) {
    var a = new Date;
    a.setTime(a.getTime() + 24 * s * 60 * 60 * 1e3);
    a = "expires=" + a.toUTCString();
    document.cookie = e + "=" + t + ";" + a + ";path=/"
}

function getCookie(e) {
    for (var t = e + "=", s = decodeURIComponent(document.cookie).split(";"), a = 0; a < s.length; a++) {
        for (var o = s[a];
            " " == o.charAt(0);) o = o.substring(1);
        if (0 == o.indexOf(t)) return o.substring(t.length, o.length)
    }
    return ""
}

const copyToClipboard = str => {
  const el = document.createElement('textarea');
  el.value = str;
  el.setAttribute('readonly', '');
  el.style.position = 'absolute';
  el.style.left = '-9999px';
  document.body.appendChild(el);
  el.select();
  document.execCommand('copy');
  document.body.removeChild(el);
  makeToast('Copiado para a área de transferência')
};

var toastTime;

function makeToast(text = null, timeOut=4000) {
  $('.toast-text').html(text)
  $('.bs-toast').fadeIn(300);

  toastTime = setTimeout(() => {
    $('.bs-toast').fadeOut(300);
  }, timeOut);
}

function removeToast() {
  $('.bs-toast').fadeOut(300);
  clearTimeout(toastTime);
}


$('.gnyFtab').click(function(){
  if($(this).hasClass('active')){
      $(this).find('.gnyFcontent').slideToggle(200);
      $(this).toggleClass('active');
  }else {
      $('.gnyFtab').removeClass('active');
      $('.gnyFtab > .gnyFcontent').slideUp(200);
      $(this).find('.gnyFcontent').slideToggle(200);
      $(this).toggleClass('active');
  }
});

$("#orderform-service").change(function () {
    service_id = $(this).val();
    $("#s_id").text(service_id);

    description = window.modules.siteOrder.services[service_id].description
    $("#s_desc").html(description);

    name = window.modules.siteOrder.services[service_id].name
    $("#s_name").html(name);
})

const headerScroll = () => {
    if (window.scrollY > 10) {
        document.querySelector('#header').classList.add('fixed');
    } else {
        document.querySelector('#header').classList.remove('fixed');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('#header')) {
        headerScroll();
    }
});

window.addEventListener('scroll', e => {
    headerScroll();
})

function noAuthMenu() {
		$('.b-menu-wrapper').toggleClass('active');
		$('body').toggleClass('stop-body');
}

function setAmount(val) {
    var setamount = document.getElementById("amount");
    setamount.value = val
}
function change_light() {
    var app = document.getElementsByTagName("BODY")[0];
    localStorage.lightMode = "light";
    app.setAttribute("class", "light");
    console.log("lightMode = " + localStorage.lightMode);
}

function change_dark() {
    var app = document.getElementsByTagName("BODY")[0];
    localStorage.lightMode = "dark";
    app.setAttribute("class", "dark");
    console.log("lightMode = " + localStorage.lightMode);
}


$(document).ready(function () {
    setList(0);
    setList(1);    
});
   
function ikon(opt) {
      var ikon = "";
    if (opt.indexOf("Instagram") >= 0) {
        ikon = "<span class=\"fs-ig\"><i class=\"fab fa-instagram\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Discord") >= 0) {
        ikon = "<span class=\"fs-ig\"><i class=\"fab fa-discord\" style=\"color: #7289da;\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Threads") >= 0) {
        ikon = "<span class=\"fs-ig\"><i class=\"ri-threads-fill\" style=\"color: #FFFFFF;\" aria-hidden=\"true\"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
    } else if (opt.indexOf("YouTube") >= 0) {
        ikon = "<span class=\"fs-yt\"><i class=\"fab fa-youtube\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Facebook") >= 0) {
        ikon = "<span class=\"fs-fb\"><i class=\"fab fa-facebook-square\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Youtube") >= 0) {
        ikon = "<span class=\"fs-yt\"><i class=\"fab fa-youtube\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Twitter") >= 0) {
        ikon = "<span class=\"fs-tw\"><i class=\"fab fa-twitter\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Google") >= 0) {
        ikon = "<span class=\"fs-gp\"><i class=\"fab fa-google-plus\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Swarm") >= 0) {
        ikon = "<span class=\"fs-fsq\"><i class=\"fab fa-forumbee\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Dailymotion") >= 0) {
        ikon = "<span class=\"fs-dm\"><i class=\"fab fa-hospital-o\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Periscope") >= 0) {
        ikon = "<span class=\"fs-pc\"><i class=\"fab fa-map-marker\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Soundcloud") >= 0) {
        ikon = "<span class=\"fs-sc\"><i class=\"fab fa-soundcloud\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Vine") >= 0) {
        ikon = "<span class=\"fs-vn\"><i class=\"fab fa-vine\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Spotify") >= 0) {
        ikon = "<span class=\"fs-sp\"><i class=\"fab fa-spotify\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Snapchat") >= 0) {
        ikon = "<span class=\"fs-snap\"><i class=\"fab fa-snapchat-square\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Pinterest") >= 0) {
        ikon = "<span class=\"fs-pt\"><i class=\"fab fa-pinterest-p\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("iTunes") >= 0) {
        ikon = "<span class=\"fs-apple\"><i class=\"fab fa-apple\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("MÃƒÂ¼zik") >= 0) {
        ikon = "<span class=\"fs-music\"><i class=\"fab fa-music\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Vimeo") >= 0) {
        ikon = "<span class=\"fs-videmo\"><i class=\"fab fa-vimeo\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("EkÃ…Å¸i") >= 0) {
        ikon = "<span class=\"fs-eksi\"><i class=\"fab fa-tint\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Telegram") >= 0) {
        ikon = "<span class=\"fs-telegram\"><i class=\"fab fa-telegram\" style=\"color: #4692DD;\" aria-hidden=\"true\"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
    } else if (opt.indexOf("Twitch") >= 0) {
        ikon = "<span class=\"fs-twc\"><i class=\"fab fa-twitch\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Zomato") >= 0) {
        ikon = "<span class=\"fs-zom\"><i class=\"fab fa-cutlery\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Amazon") >= 0) {
        ikon = "<span class=\"fs-amaz\"><i class=\"fab fa-amazon\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Tumblr") >= 0) {
        ikon = "<span class=\"fs-tumb\"><i class=\"fab fa-tumblr-square\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Yandex") >= 0) {
        ikon = "<span class=\"fs-yndx\"><i class=\"fab fa-yoast\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Linkedin") >= 0) {
        ikon = "<span class=\"fs-lnk\"><i class=\"fab fa-linkedin\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("Favorilerim") >= 0) {
        ikon = "<span class=\"fs-fav\"><i class=\"far fa-star\" aria-hidden=\"true\"></i> </span>";
    } else if (opt.indexOf("TikTok") >= 0) {
        ikon = "<span class=\"fs-tiktok\"><i class=\"fa fa-music\" aria-hidden=\"true\"></i> </span>";
    } else {
        ikon = "<span class=\"\"><i class=\"ri-shield-check-fill\" style=\"color: #64CD8A;\" aria-hidden=\"true\"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";
    }
    return ikon;
   }


function setList(val) {
    /* orders */
    if (val == 0) {
        $("#orders-drop").empty();
        $("#neworder_services option").each(function () {
            var ico = ikon($(this).text());
            $("#orders-drop").append('<button id="serviceItem" class="dropdown-item" type="button" onclick="selectOrder(' + $(this).val() + ')">' + ico + $(this).text() + "</button>");
        });
        /*if(this.selected) {*/
        var e = document.getElementById("neworder_services");
        var selected = $( "#neworder_services option:selected" ).text();
        var ico = ikon(selected);
        $("#serviceTitle").html(ico + selected);
        /*}else {
       var ico = ikon($("#neworder_services option:nth-child(1)").text());              
        $("#serviceTitle").html(ico + $("#neworder_services option:nth-child(1)").text());         
                  }            */
    } else if (val == 1) {
        /* SERVICES */

        $("#category-drop").empty();
        $("#neworder_category option").each(function () {
            var ico = ikon($(this).text());
            $("#category-drop").append('<button id="categoryItem" class="dropdown-item" type="button" onclick="selectCategory(' + $(this).val() + ')">' + ico + $(this).text() + "</button>");
        });

        /* if(this.selected) {*/
        var e = document.getElementById("neworder_category");
        var selected = e.options[e.selectedIndex].text;
        var ico = ikon(selected);
        $("#categoryTitle").html(ico + selected);
        /*}else {      
        var ico = ikon($("#neworder_category option:nth-child(1)").text());              
        $("#categoryTitle").html(ico + $("#neworder_category option:nth-child(1)").text());
                 } */
    }
}
$(function (ready) {
    $("#neworder_services").change(function () {
        setList(0);
    });
    $("#neworder_category").change(function () {
        setList(1);
    });
});

function selectOrder(val) {
    $("#neworder_services").val(val);
    $("#neworder_services").trigger("change");
    var ico = ikon($("#neworder_services option[value='" + val + "']").text());
    $("#serviceTitle").html(ico + $("#neworder_services option[value='" + val + "']").text());
}
$("#serviceItem").click(function () {
    $("#serviceTitle").html($(this).html());
});

function selectCategory(val) {
    $("#neworder_category").val(val);
    $("#neworder_category").trigger("change");
    var ico = ikon($("#neworder_category option[value='" + val + "']").text());
    $("#categoryTitle").html(ico + $("#neworder_category option[value='" + val + "']").text());
}

function change_mode() {

		var app = document.getElementsByTagName("BODY")[0];

		if (localStorage.lightMode == "dark") {
			localStorage.lightMode = "light";
			app.setAttribute("class", "light");
		} else {
			localStorage.lightMode = "dark";
			app.setAttribute("class", "dark");
		}
		console.log("lightMode = " + localStorage.lightMode);
}
