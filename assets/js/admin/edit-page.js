const ajaxNonce = window.wpvme.ajax_nonce;
const ajaxUrl = window.wpvme.ajax_url;

const runFetchWithAjax = async (fetchAction, options = {}) => {
  const action = `wpvme_${fetchAction}`;
  const params = new URLSearchParams();
  params.append('action', action);
  params.append('ajax_nonce', ajaxNonce);

  Object.keys(options).forEach((key) => {
    params.append(key, options[key]);
  });

  try {
    const response = await fetch(ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: params,
    });
    const result = await response.json();
    if (result?.success) {
      return result;
    }

    throw new Error(result.data);
  } catch (error) {
    return {
      success: false,
      data: error.message,
    };
  }
};

window.onload = () => {
  let engineName = 'os';
  let mapName = '';
  const addNewBtn = document.querySelector('.page-title-action');
  const createMapBtn = document.querySelector(
    '.wme-add-new-map-popup-wrapper .wme-anm-btn'
  );
  const popupWrapper = document.querySelector('.wme-add-new-map-popup-wrapper');
  const closeBtn = document.querySelector(
    '.wme-add-new-map-popup-wrapper .wme-anm-close-btn'
  );
  const engines = document.querySelectorAll(
    '.wme-add-new-map-popup-wrapper .wme-anm-engine-selector'
  );
  const mapNameInput = document.querySelector(
    '.wme-add-new-map-popup-wrapper .wme-anm-name'
  );
  const errorDiv = document.querySelector(
    '.wme-add-new-map-popup-wrapper .wme-anm-error'
  );

  const shortCodeInput = document.querySelectorAll('.me-shortcode-copy');

  addNewBtn.style.display = 'inline-block';

  addNewBtn.addEventListener('click', (e) => {
    e.preventDefault();
    openPopup();
  });

  createMapBtn.addEventListener('click', async (e) => {
    e.preventDefault();
    if (!mapName) {
      errorDiv.style.display = 'block';
      errorDiv.innerHTML = 'Map name can not be empty!';
      return;
    }

    createMapBtn.innerHTML = 'Creating...';
    createMapBtn.disabled = true;
    errorDiv.style.display = 'none';
    errorDiv.innerHTML = '';

    const result = await runFetchWithAjax('create_map', {
      map_name: mapName,
      engine: engineName,
    });
    if (result.success) {
      window.location.href = result.data.redirect_url;
    } else {
      errorDiv.style.display = 'block';
      errorDiv.innerHTML = result.data;
      createMapBtn.innerHTML = 'Create map';
      createMapBtn.disabled = false;
    }
  });

  engines.forEach((engine) => {
    engine.addEventListener('click', (e) => {
      engineName = e.currentTarget.dataset.engine;
      engines.forEach((engine) => {
        engine.classList.remove('selected');
      });
      if (engineName === 'google') {
        if (wpvme.gm_api_key === '') {
          document.querySelector('.wme-google-api-notice.hide').classList.remove('hide');
          document.querySelector('.wme-anm-btn').disabled = true;
        }
      } else {
        document.querySelector('.wme-google-api-notice').classList.add('hide');
        document.querySelector('.wme-anm-btn').disabled = false;
      }
      engine.classList.add('selected');
    });
  });

  mapNameInput.addEventListener('keyup', (e) => {
    mapName = e.target.value;
  });

  closeBtn.addEventListener('click', (e) => {
    e.preventDefault();
    closePopup();
  });

  popupWrapper.addEventListener('click', (e) => {
    if (e.target.classList.contains('wme-add-new-map-popup-wrapper')) {
      closePopup();
    }
  });

  shortCodeInput.forEach((shortcode) => {
    shortcode.addEventListener('click', (e) => {
      e.target.previousElementSibling.select();
      e.target.parentElement.nextElementSibling.style.opacity = '1';
      setTimeout(() => {
        // document.querySelector('.wme-copied-notice').style.opacity = '0';
        e.target.parentElement.nextElementSibling.style.opacity = '0';
      }, 2000);
      navigator.clipboard.writeText(e.target.previousElementSibling.value);
    });
  });

  const closePopup = () => {
    popupWrapper.style.display = 'none';
  };

  const openPopup = () => {
    popupWrapper.style.display = 'block';
  };

  const copyShortcode = () => {};
};
