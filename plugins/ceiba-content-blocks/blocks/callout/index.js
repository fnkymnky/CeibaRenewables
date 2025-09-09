import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';
import Edit from './edit';
import './style.scss';

registerBlockType('ceiba/callout', {
  edit: Edit,
  // Persist inner blocks so content remains after save/reload
  save: () => <InnerBlocks.Content />,
});
