import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import './style.scss';

registerBlockType('ceiba/testimonial-carousel', { edit: Edit, save: () => null });

