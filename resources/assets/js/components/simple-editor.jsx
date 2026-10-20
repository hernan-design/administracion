import React from 'react';

// app components
import ProjectEditor from './projecteditor';

const defaults = {
    toolbar: [
        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
        ['para', ['ul', 'ol', 'paragraph', 'hr', 'height']],
        ['insert', ['link']]
    ]
};

const SimpleEditor = ({ ...props }) => (
    <ProjectEditor
        options={defaults}
        {...props}
    />
);

export default SimpleEditor
