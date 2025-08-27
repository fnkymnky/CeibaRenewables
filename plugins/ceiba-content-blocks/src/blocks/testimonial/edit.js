import { __ } from '@wordpress/i18n';
import { useMemo } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RadioControl, Spinner } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
  const { postId } = attributes;
  const blockProps = useBlockProps({ className: 'is-edit' });

  const { posts, isResolving } = useSelect((select) => {
    const s = select(coreStore);
    const q = { per_page: 100, order: 'desc', orderby: 'date' };
    return {
      posts: s.getEntityRecords('postType', 'testimonial', q),
      isResolving: s.isResolving('getEntityRecords', ['postType', 'testimonial', q]),
    };
  }, []);

  const options = useMemo(() => {
    const items = (posts || []).map((p) => ({
      label: p.title?.rendered ? p.title.rendered.replace(/<[^>]+>/g, '') : `#${p.id}`,
      value: String(p.id),
    }));
    return [{ label: __('— None —', 'ceiba'), value: '' }, ...items];
  }, [posts]);

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Testimonial', 'ceiba')} initialOpen>
          {isResolving ? (
            <Spinner />
          ) : (
            <RadioControl
              selected={postId ? String(postId) : ''}
              options={options}
              onChange={(val) => setAttributes({ postId: val ? parseInt(val, 10) : 0 })}
            />
          )}
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className="ceiba-testimonial__placeholder">
          <strong>{__('Testimonial (Single)', 'ceiba')}</strong>
          <div className="hint">
            {postId ? __('Selected. Front-end shows preview.', 'ceiba') : __('Pick one in the sidebar.', 'ceiba')}
          </div>
        </div>
      </div>
    </>
  );
}
