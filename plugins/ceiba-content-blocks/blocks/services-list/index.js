/* Services List block (editor stub only; front-end is rendered in PHP) */
// Import styles so @wordpress/scripts compiles style.scss -> style-index.css
import './style.scss';

(function(){
  function register() {
    const { registerBlockType } = wp.blocks;
    const { createElement: el, useMemo } = wp.element;
    const { useBlockProps, InspectorControls } = wp.blockEditor || wp.editor;
    const { PanelBody, SelectControl, Spinner } = wp.components;
    const { useSelect } = wp.data;

    registerBlockType('ceiba/page-list', {
      title: 'Page List',
      description: 'Displays direct child pages of a selected parent page in a 3-column grid.',
      icon: 'grid-view',
      category: 'widgets',
      supports: { anchor: true, align: ['wide','full'], spacing: { padding: true, margin: true } },
      attributes: { parentId: { type: 'number' } },
      edit({ attributes, setAttributes }) {
        const { parentId } = attributes || {};
        const blockProps = useBlockProps ? useBlockProps({ className: 'ceiba-page-list is-editor-stub' }) : {};

        const { pages, isResolving } = useSelect( (select) => {
          const core = select('core');
          const query = { per_page: 100, order: 'asc', orderby: 'title', status: 'publish' };
          const items = core && core.getEntityRecords ? core.getEntityRecords('postType', 'page', query) : null;
          const resolving = core && core.isResolving ? core.isResolving('getEntityRecords', ['postType','page', query]) : false;
          return { pages: items, isResolving: resolving };
        }, []);

        const options = useMemo(() => {
          if (!pages || !Array.isArray(pages)) return [];
          return pages.map((p) => ({ label: p.title?.rendered || '(no title)', value: p.id }));
        }, [pages]);

        return el(wp.element.Fragment, null,
          el(InspectorControls, null,
            el(PanelBody, { title: 'Source', initialOpen: true },
              isResolving && el(Spinner, null),
              !isResolving && el(SelectControl, {
                label: 'Parent page (required)',
                value: parentId || '',
                options,
                onChange: (val) => setAttributes({ parentId: val ? parseInt(val, 10) : undefined })
              })
            )
          ),
          el('div', blockProps,
            el('strong', null, 'Page List'),
            el('div', { style: { color: '#6b7280', marginTop: 4 } }, parentId ? 'Showing direct child pages of the selected parent.' : 'Select a parent page in block settings to render child pages.')
          )
        );
      },
      save() { return null; }
    });
  }

  // Wait until wp.* globals are present (no asset dependencies in source mode)
  (function wait(){
    if (window.wp && wp.blocks && wp.element && (wp.blockEditor || wp.editor) && wp.components && wp.data) {
      register();
    } else {
      setTimeout(wait, 25);
    }
  })();
})();
