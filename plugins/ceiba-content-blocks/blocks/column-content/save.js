import { InnerBlocks } from '@wordpress/block-editor';

export default function save() {
  // Save inner blocks markup so it renders even if dynamic render isn't wired
  return <InnerBlocks.Content />;
}
