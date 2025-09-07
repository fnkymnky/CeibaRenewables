import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import './style.scss';

registerBlockType('ceiba/insights-grid', { edit: Edit, save: () => null });

