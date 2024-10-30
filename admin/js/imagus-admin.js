(function (global) {
  function make_payload(props) {
    props = props || {};
    let id = props.id;
    let quality = props.quality;
    let local_copy = props.local_copy;
    const params = new URLSearchParams();
    params.append('action', 'imagus_optimize_single');
    params.append('id', id);
    params.append('quality_image', quality);
    params.append('original_local_copy', local_copy);
    return params.toString();
  }

  function settings(){
    return new Promise(function (resolve, reject) {
      fetch("/wp-admin/admin-ajax.php?action=imagus_get_settings", {
        method: "GET",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          "Accept": "application/json",
        },
        credentials: "same-origin"
      }).then(function (res) {
        if (!res.ok) {
          throw new Error(response.statusText);
        }
        return res.json();
      }).catch(function (err) {
        reject(err)
      }).then(function (data) {
        if (data.ok) {
          resolve(data.message);
        } else {
          let error = new Error(data.message);
          error.code = data.code;
          reject(error);
        }
      })
    })
  }

  function start_loading(image_id) {
    let spinner = document.querySelector('#imagus-spinner-' + image_id);
    spinner.classList.remove('imagus-display-none');
    document.querySelector('#imagus-optimizer-' + image_id).classList.add('imagus-display-none');
  }

  function stop_loading(image_id) {
    console.log('#imagus-spinner-' + image_id);
    let spinner = document.querySelector('#imagus-spinner-' + image_id);
    spinner.classList.add('imagus-display-none');
  }

  function toggle_loading(image_id) {
    let loading_col = document.querySelector('#imagus-media-column-' + image_id);
    let loading = loading_col.dataset.loading;
    if ("true" === loading) {
      stop_loading(image_id);
      loading_col.dataset.loading = "false";
    } else {
      start_loading(image_id);
      loading_col.dataset.loading = "true";
    }
  }

  function optimize_single(props) {
    return new Promise(function (resolve, reject) {
      toggle_loading(props.id);
      optimize(props)
        .then(function (data) {
          append_result(data, props.id);
        })
        .catch(function (err) {
          append_error(err, props.id);
        })
        .finally(function () {
          toggle_loading(props.id);
          resolve();
        });
    });
  }

  function optimize(props) {
    return new Promise(function (resolve, reject) {
      fetch("/wp-admin/admin-ajax.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          "Accept": "application/json",
        },
        body: make_payload(props),
        credentials: "same-origin"
      }).then(function (res) {
        if (!res.ok) {
          throw new Error(response.statusText);
        }
        return res.json();
      }).catch(function (err) {
        reject(err)
      }).then(function (data) {
        if (data.ok) {
          resolve(data.message);
        } else {
          let error = new Error(data.message);
          error.code = data.code;
          reject(error);
        }
      })
    })
  }

  function append_result(data, image_id) {
    let container = document.querySelector('#imagus-media-column-' + image_id);
    container.textContent = '';

    if (data.bytes_saved === 0){
      jQuery(container).append(`
              <div class="imagus-message-${image_id}"><small>${imagus_admin_vars.zero_saved}</small> </div>
          `);
    }
    else {
      jQuery(container).append(`
              <div class="imagus-message-${image_id}">
              <small class="imagus-label success">${imagus_admin_vars.optimized}</small>
              </div>
              <div class="imagus-bytes-saved">
              <small class="imagus-label other">${data.human_bytes_saved} saved</small>
              </div>
              <div class="imagus-percentage-saved">
              <small class="imagus-label other">${data.bytes_percentage}% saved</small>
              </div>
          `);
    }
    container.dataset.optimized = 'true';
  }

  function append_error(err, image_id) {
    let container = document.querySelector('#imagus-media-column-' + image_id);
    jQuery(container).append(`
            <div> Error: ${err.message} </div>
        `);
    document.querySelector('#imagus-optimizer-' + image_id).classList.remove('imagus-display-none');
  }

  function main() {
    global.imagus = {
      optimize: optimize,
      optimize_single: optimize_single,
      start_loading: start_loading,
      stop_loading: stop_loading,
      toggle_loading: toggle_loading,
      settings: settings
    };
    document.addEventListener("click", function (e) {
      if (e.target.className.indexOf("imagus-optimizer") === -1) {
        return;
      }
      const id = e.target.dataset.imagus;
      document.dispatchEvent(ImagusEvent([id]));
    });

    jQuery('#bulk-action-selector-top').append('<option value="imagus">'+imagus_admin_vars.compression+'</option>');
    jQuery('#bulk-action-selector-bottom').append('<option value="imagus">'+imagus_admin_vars.compression+'</option>');
    jQuery('#doaction, #doaction2').addClass('imagus-selector');

    function ImagusEvent(ids) {
      const event = new Event('imagus::optimize');
      event.data = {
        ids: ids
      };
      return event;
    }

    document.addEventListener('click', function (e) {
      if (!e.target.classList.contains('imagus-selector')) {
        return;
      }

      let value = e.target.parentElement.querySelector('select').value;

      if (value !== 'imagus') {
        return;
      }
      e.preventDefault();
      let imageIds = Array.from(document.querySelectorAll('.wp-list-table tbody input[type="checkbox"]:checked'))
        .map(function (e) {
          return e.value;
        })
        .filter(function (id) {
          return document.querySelector('#imagus-media-column-' + id).dataset.optimized === 'false';
        });
      document.dispatchEvent(ImagusEvent(imageIds));

    });

    document.addEventListener("click", function (e) {
      let id = e.target.id;
      let id_pattern = /recover-image/g;
      let match = id_pattern.exec(id);

      if (null === match) {
        return;
      }

      e.preventDefault();

      jQuery.ajax({
        type : "post",
        url : '/wp-admin/admin-ajax.php',
        data : {
          action: "imagus_recover_image",
          image_id : e.target.dataset.image
        },
        error: function(response){
          console.log(response);
        },
        success: function(response) {
          console.log(response);
          location.reload();
        }
      });
    });
  }

  document.addEventListener("DOMContentLoaded", main)
}(window));
