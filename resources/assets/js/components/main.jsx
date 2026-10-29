import React from 'react';
import ReactDOM from 'react-dom';
import { createRoot } from 'react-dom/client';
import ProjectEditor from './projecteditor';
import SimpleEditor from './simple-editor';

const roots = new WeakMap();

const customReactDOM = {
    ...ReactDOM,
    render(element, container) {
        if (!container) return;
        let root = roots.get(container);
        if (!root) {
            root = createRoot(container);
            roots.set(container, root);
        }
        root.render(element);
    }
};

window.React = React;
window.ReactDOM = customReactDOM;
window.ProjectEditor = ProjectEditor;
window.SimpleEditor = SimpleEditor;

