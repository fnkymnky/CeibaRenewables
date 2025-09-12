import { __ } from '@wordpress/i18n';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

const ALLOWED = ['core/image','core/heading','core/paragraph','core/list','core/list-item','core/buttons'];
const TEMPLATE = [
  ['core/image'],
  ['core/heading', { level: 3 }],
  ['core/paragraph']
];

export default function Edit() {
  const blockProps = useBlockProps({ className: 'ccb-col' });
  return (
    <article {...blockProps}>
      <InnerBlocks allowedBlocks={ALLOWED} template={TEMPLATE} templateLock={false} />
    </article>
  );
}

