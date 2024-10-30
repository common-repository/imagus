jQuery(function () {
  let modal = jQuery("#imagus-optimize-dialog").dialog({
      autoOpen: false,
      draggable: false,
      resizable: false,
      show: 'fade',
      hide: 'drop'
    }
  );

  document.addEventListener("imagus::optimize", function (e) {
    window.imagus.settings().then( function(data){
      if ('1' === data.modal_window_options){
        let form = document.querySelector(".form-table");
        form.dataset.targets = JSON.stringify(e.data.ids);
        jQuery(".form-table #imagus-image-quality").val(data.quality_image);
        jQuery(".form-table #imagus-original-copies").prop('checked', data.original_local_copy !== '0');
        modal.dialog('open');
      }
      else{
        request_optimization(e.data.ids, {
          quality_image: parseInt(data.quality_image),
          original_local_copy: data.original_local_copy !== '0'
        })
      }
    });
  });

  document.addEventListener("click", function (e) {
    if (e.target.id !== "imagus-single-submit") {
      return;
    }

    let form = e.target.closest(".form-table");
    let ids = JSON.parse(form.dataset.targets);
    let settings = {
      'quality_image' : parseInt(form.querySelector('#imagus-image-quality').value),
      'original_local_copy' : form.querySelector("#imagus-original-copies").checked
    };
    request_optimization(ids,settings);
    modal.dialog('close');
  });

  function request_optimization(ids,settings){
    let promises = ids.map(function (id) {
      return window.imagus.optimize_single({
        id: id,
        quality: settings.quality_image,
        local_copy: settings.original_local_copy
      })
    });
    return new Promise(function(resolve){
      Promise.all(promises).then(resolve);
    });
  }
});
