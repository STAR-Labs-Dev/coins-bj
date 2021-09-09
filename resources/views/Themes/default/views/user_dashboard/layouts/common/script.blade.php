<script src="{{theme_asset('public/js/jquery.min.js')}}" type="text/javascript"></script>
<!-- popper.min.js must place before bootstrap.min.js, else won't work-->
<script src="{{theme_asset('public/js/popper.min.js')}}" type="text/javascript"></script>
<script src="{{theme_asset('public/js/bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{theme_asset('public/js/jquery.waypoints.min.js')}}" type="text/javascript"></script>
<script src="{{theme_asset('public/js/main.js')}}" type="text/javascript"></script>
<script src="{{theme_asset('public/js/moment.js')}}" type="text/javascript"></script>

{!! getGoogleAnalyticsTrackingCode() !!}

<script type="text/javascript">
	$('#delete-warning-modal').on('show.bs.modal', function (e) {
		$message  = $(e.relatedTarget).attr('data-message');
		$(this).find('.modal-body p').text($message);
		$title    = $(e.relatedTarget).attr('data-title');
		$(this).find('.modal-title').text($title);

		// Pass form reference to modal for submission on yes/ok
		var form  = $(e.relatedTarget).closest('form');
		$(this).find('.modal-footer #delete-modal-yes').data('form', form);
	});

	$('#delete-warning-modal').find('.modal-footer #delete-modal-yes').on('click', function(e){
		$(this).data('form').submit();
	});
</script>

<script type="text/javascript">

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	function log(log) {
		console.log(log);
	}

	$(function () {
		$('[data-toggle="tooltip"]').tooltip();

		if ($('#subStringUserName').html().length > 20) {
			$('#subStringUserName').tooltip({placement: "left"}); //subStringUserName tooltip
		}
	})

	function resizeHeaderOnScroll() {
		const distanceY = window.pageYOffset || document.documentElement.scrollTop,
			shrinkOn = 100,
			headerEl = document.getElementById('js-header');

		if (headerEl) {
			if (distanceY > shrinkOn) {
				headerEl.classList.add("smaller-header");
				$("#logo_container").attr('src', SITE_URL + '/public/frontend/images/logo_sm.png');
			} else {
				headerEl.classList.remove("smaller-header");
				$("#logo_container").attr('src', SITE_URL + '/public/frontend/images/logo.png');
			}
		}
	}
	window.addEventListener('scroll', resizeHeaderOnScroll);

	//Language - user_dashboard
	$('#lang').on('change', function (e) {
		e.preventDefault();
		lang = $(this).val();
		url = '{{url('change-lang')}}';
		$.ajax({
			type: 'get',
			url: url,
			data: {lang: lang},
			success: function (msg) {
				if (msg == 1) {
					location.reload();
				}
			}
		});
	});

// custom dropdown
function create_custom_dropdowns() {
$('.select-custom').each(function(i, select) {
	if (!$(this).next().hasClass('dropdown')) {
	$(this).after('<div class="dropdown ' + ($(this).attr('class') || '') + '" tabindex="0"><span class="current"></span><div class="list"><ul></ul></div></div>');
	var dropdown = $(this).next();
	var options = $(select).find('option');
	var selected = $(this).find('option:selected');
	dropdown.find('.current').html(selected.data('display-text') || selected.text());
	options.each(function(j, o) {
		var display = $(o).data('display-text') || '';
		dropdown.find('ul').append('<li class="option ' + ($(o).is(':selected') ? 'selected' : '') + '" data-value="' + $(o).val() + '" data-display-text="' + display + '">' + $(o).text() + '</li>');
	});
	}
});
}

// Event listeners

// Open/close
$(document).on('click', '.dropdown', function(event) {
    $('.dropdown').not($(this)).removeClass('open');
    $(this).toggleClass('open');
    if ($(this).hasClass('open')) {
        $(this).find('.option').attr('tabindex', 0);
        $(this).find('.selected').focus();
    } else {
        $(this).find('.option').removeAttr('tabindex');
        $(this).focus();
    }
});
// Close when clicking outside
$(document).on('click', function(event) {
    if ($(event.target).closest('.dropdown').length === 0) {
        $('.dropdown').removeClass('open');
        $('.dropdown .option').removeAttr('tabindex');
    }
    event.stopPropagation();
});
// Option click
$(document).on('click', '.dropdown .option', function(event) {
	$(this).closest('.list').find('.selected').removeClass('selected');
	$(this).addClass('selected');
	var text = $(this).data('display-text') || $(this).text();
	$(this).closest('.dropdown').find('.current').text(text);
	$(this).closest('.dropdown').prev('select').val($(this).data('value')).trigger('change');
});

// Keyboard events
$(document).on('keydown', '.dropdown', function(event) {
	var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
	// Space or Enter
	if (event.keyCode == 32 || event.keyCode == 13) {
		if ($(this).hasClass('open')) {
		    focused_option.trigger('click');
		} else {
		    $(this).trigger('click');
		}
		return false;
		// Down
	} else if (event.keyCode == 40) {
		if (!$(this).hasClass('open')) {
		    $(this).trigger('click');
		} else {
		    focused_option.next().focus();
		}
		return false;
		// Up
	} else if (event.keyCode == 38) {
		if (!$(this).hasClass('open')) {
		    $(this).trigger('click');
		} else {
		    var focused_option = $($(this).find('.list .option:focus')[0] || $(this).find('.list .option.selected')[0]);
		    focused_option.prev().focus();
		}
		return false;
	// Esc
	} else if (event.keyCode == 27) {
		if ($(this).hasClass('open')) {
		    $(this).trigger('click');
		}
		return false;
	}
});

$(document).ready(function() {
	create_custom_dropdowns();
});

</script>
