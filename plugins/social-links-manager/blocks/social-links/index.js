/* global wp */
(function() {
  const { registerBlockType } = wp.blocks;
  const { __ } = wp.i18n;
  const { createElement: el, Fragment } = wp.element;
  const { InspectorControls, useBlockProps } = wp.blockEditor || wp.editor;
  const { PanelBody, ToggleControl, Notice } = wp.components;
  const ServerSideRender = wp.serverSideRender;

  const Networks = [
    { key: 'Facebook', label: 'Facebook' },
    { key: 'Twitter', label: 'Twitter' },
    { key: 'Instagram', label: 'Instagram' },
    { key: 'Linkedin', label: 'LinkedIn' },
    { key: 'Whatsapp', label: 'WhatsApp' },
    { key: 'Snapchat', label: 'Snapchat' },
    { key: 'Tiktok', label: 'TikTok' },
    { key: 'Youtube', label: 'YouTube' },
  ];

  registerBlockType('slm/social-links', {
    title: __('Social Links', 'social-links-manager'),
    description: __('Display saved social links from Settings → Contact Details.', 'social-links-manager'),
    icon: 'share',
    category: 'widgets',
    supports: { html: false, align: [ 'left', 'center', 'right' ] },
    edit(props) {
      const { attributes, setAttributes } = props;

      // No preview attribute needed; PHP disables links in admin automatically.

      const blockProps = useBlockProps ? useBlockProps() : {};
      return el('div', blockProps,
        el(InspectorControls, null,
          el(PanelBody, { title: __('Visible Networks', 'social-links-manager'), initialOpen: true },
            Networks.map(n => el(ToggleControl, {
              key: n.key,
              label: n.label,
              checked: attributes['show' + n.key] !== false,
              onChange: (val) => setAttributes({ ['show' + n.key]: !!val })
            }))
          )),
        el(InspectorControls, null,
          el(PanelBody, { title: __('Appearance', 'social-links-manager'), initialOpen: false },
            el(ToggleControl, {
              label: __('Use brand colors', 'social-links-manager'),
              checked: !!attributes.brandColors,
              onChange: (val) => setAttributes({ brandColors: !!val })
            })
          )
        ),
        el(ServerSideRender, { block: 'slm/social-links', attributes }),
        el(Notice, { status: 'info', isDismissible: false, className: 'slm-social-links-help' },
          el('span', null,
            __('Only icons for links saved in Settings → Contact Details will show. ', 'social-links-manager'),
            el('a', { href: '/wp-admin/options-general.php?page=social-links-manager' }, __('Open settings', 'social-links-manager'))
          )
        )
      );
    },
    save() {
      return null;
    },
  });
})();
