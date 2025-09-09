import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import './style.scss';

registerBlockType('ceiba/insights-carousel', { edit: Edit, save: () => null });

