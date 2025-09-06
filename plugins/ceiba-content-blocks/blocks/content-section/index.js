import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import './style.scss';
import './editor.css';

registerBlockType('ceiba/content-section', { edit: Edit, save });
