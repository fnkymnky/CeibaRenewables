(function (blocks, element, serverSideRender) {
  const { registerBlockType } = blocks;
  const el = element.createElement;
  registerBlockType('slm/contact-info', {
    edit() { return el(serverSideRender, { block: 'slm/contact-info' }); },
    save() { return null; },
  });
})(wp.blocks, wp.element, wp.serverSideRender);
