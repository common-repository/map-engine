window.addEventListener('DOMContentLoaded', (event) => {
  const mapContainer = document.querySelectorAll('.wpv-me-map-container');

  const topMenu = document.querySelector('.map-engine-top-bar');
  if (!mapContainer.length) {
    document.querySelector('#wp-admin-bar-me-maps').style.display = 'none';
  }
  mapContainer.forEach((map) => {
    const subMenu = topMenu.querySelector('.ab-sub-wrapper');
    const { mapId } = map.dataset;
    const { mapName } = map.dataset;

    if (!subMenu) {
      topMenu.classList.add('menupop');
      topMenu.querySelector('.ab-item').setAttribute('aria-haspopup', 'true');
      topMenu.addEventListener('mouseover', (e) => {
        topMenu.classList.add('hover');
      });
      topMenu.addEventListener('mouseout', (e) => {
        topMenu.classList.remove('hover');
      });
      const topDiv = document.createElement('div');
      // topDiv.id = 'wp-admin-bar-me-maps-default';
      topDiv.classList = 'ab-sub-wrapper';
      topMenu.appendChild(topDiv);

      const subMenuUl = document.createElement('ul');
      subMenuUl.id = 'wp-admin-bar-me-maps-default';
      subMenuUl.classList = 'ab-submenu';
      topDiv.appendChild(subMenuUl);

      addSubMenu(mapId, mapName, subMenuUl);
    } else {
      const subMenuUl = topMenu.querySelector('.ab-sub-wrapper ul');
      addSubMenu(mapId, mapName, subMenuUl);
    }
  });
});

function addSubMenu(mapId, mapName, ul) {
  const subMenuItem = document.createElement('li');
  subMenuItem.id = `wp-admin-bar-me-item-${mapId}`;
  subMenuItem.classList = 'my_menu_item_class';
  ul.appendChild(subMenuItem);
  const postEditUrl = `${wpvme.admin_url}post.php?post=${mapId}&action=edit`;

  const subMenuAnchor = document.createElement('a');
  subMenuAnchor.href = postEditUrl;
  subMenuAnchor.classList = 'ab-item';
  subMenuAnchor.text = `Map - ${mapName}`;
  subMenuItem.appendChild(subMenuAnchor);
}
