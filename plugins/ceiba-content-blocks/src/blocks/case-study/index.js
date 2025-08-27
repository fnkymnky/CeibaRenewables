import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import './style.scss';

registerBlockType('ceiba/case-study', {
  edit: Edit,
  save, // dynamic -> returns null
});
