import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import { Button, Modal } from '@wordpress/components';

const ALLOWED = ['core/image', 'core/heading', 'core/paragraph', 'core/details'];
const TEMPLATE = [
  ['core/image'],
  ['core/heading', { level: 3, placeholder: __('Name', 'ceiba') }],
  ['core/heading', { level: 4, placeholder: __('Job title', 'ceiba') }],
  ['core/details', {}, [
    ['core/paragraph', { placeholder: __('Short bio…', 'ceiba') }]
  ]]
];

export default function Edit() {
  const [isOpen, setOpen] = useState(false);
  const blockProps = useBlockProps({ className: 'ceiba-team-grid__item is-placeholder' });

  return (
    <div {...blockProps}>
      <div className="ceiba-team-grid__item-preview">
        <div style={{ opacity: 0.7 }}>{__('Team member', 'ceiba')}</div>
        <Button variant="primary" onClick={() => setOpen(true)} style={{ marginTop: 8 }}>
          {__('Edit member', 'ceiba')}
        </Button>
      </div>

      {isOpen && (
        <Modal title={__('Team Member', 'ceiba')} onRequestClose={() => setOpen(false)}>
          <InnerBlocks
            allowedBlocks={ALLOWED}
            template={TEMPLATE}
            templateLock={false}
            renderAppender={InnerBlocks.ButtonBlockAppender}
          />
          <div style={{ marginTop: 12, textAlign: 'right' }}>
            <Button variant="primary" onClick={() => setOpen(false)}>
              {__('Done', 'ceiba')}
            </Button>
          </div>
        </Modal>
      )}
    </div>
  );
}

