import React, { Component } from 'react';
import PropTypes from 'prop-types';
import $ from 'jquery';
window.jQuery = $;
window.$ = $;

import ReactSummernote from './react-summernote.jsx';

import Backbone from 'backbone';
import '../helpers/misc.js';
const Misc = window.Misc;
const { formToJson, getCsrf, removeSpinner, setSpinner } = Misc;
const laroute = window.Route;

class ProjectEditor extends Component {

    constructor(props) {
        super(props);

        // initialize attributes
        let data = props.formCreate ? formToJson(props.formCreate) : {};
        this.defaultValue = props.content.get(props.nameField) || data[props.nameField] || '';
        props.content.set(props.nameField, this.defaultValue, { silent: true });

        // set default states
        this.state = {
            value: this.defaultValue
        };

        // events handler
        this.handleChange = this.handleChange.bind(this);
        this.imageUpload = this.imageUpload.bind(this);
        props.content.on(`change:${props.nameField}`, this.writeValue, this);
    }

    componentDidMount() {
        const $nodeEditor = $(this.nodeEditor);

        $nodeEditor.find('[data-toggle="tooltip"]').tooltip();
        $nodeEditor.find('[data-toggle="dropdown"]').each(function (index, el) {
            const $el = $(el);

            $el.dropdown();
        });
    }

    componentWillUnmount() {
        this.props.content.off(`change:${this.props.nameField}`, this.writeValue, this);

        const $nodeEditor = $(this.nodeEditor);

        $nodeEditor.find('[data-toggle="tooltip"]').tooltip('destroy');
        $nodeEditor.find('[data-toggle="dropdown"]').off('bs.dropdown');
    }

    componentWillReceiveProps(nextProps) {

        let data = nextProps.formCreate ? formToJson(nextProps.formCreate) : {};
        this.defaultValue = nextProps.content.get(nextProps.nameField) || data[nextProps.nameField] || '';

        this.writeValue(nextProps.content, this.defaultValue);
    }

    /**
    * @param content
    * @param value
    * write value for state
    */
    writeValue(content, value) {
        this.setState({ value: value });
    }

    /**
    * @param model
    * handler when editor model change
    */
    handleChange(content) {
        // validate if the editor content is really empty
        if (this.editor && typeof this.editor.isEmpty === 'function' && this.editor.isEmpty()) {
            this.writeValue(this.props.content, '');
        } else {
            this.writeValue(this.props.content, content);
        }

        if (this.props.onChange) this.props.onChange(content);
    }

    /*
    * reference editor
    */
    refEditor(editor) {
        this.editor = editor;
    }

    /**
    * @param files
    * images upload change
    */
    imageUpload(files) {
        const dataFile = files[0];
        const noteEditor = this.editor.noteEditable;

        // load spinner
        setSpinner(noteEditor);

        let data = new FormData();
        data.append('file', dataFile);

        let customHeaders = new Headers({
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content'),
            "X-XSRF-TOKEN": getCsrf().getCookie('XSRF-TOKEN'),
        });

        fetch(Misc.urlFull(laroute.route('file.upload')), {
            method: "POST",
            credentials: "same-origin",
            headers: customHeaders,
            body: data
        }).then(response => {
            return response.json();
        }).then(data => {
            this.editor.insertImage(data.file_path);
            // remove spinner
            removeSpinner({
                wrap: noteEditor,
                direct: false
            });
        }).catch(error => ({
            error,
            infoStatus: 'error'
        }));
    }

    render() {
        let defaults = {
            lang: 'es-ES',
            minHeight: 250,
            dialogsInBody: true,
            placeholder: this.props.placeholder,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['fontstyle', ['superscript', 'subscript']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph', 'hr', 'height']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview']],
                ['help', ['undo', 'redo', 'help']]
            ],
            tabsize: 2,
            prettifyHtml: true,
            codemirror: {
                indentWithTabs: true,
                lineNumbers: true,
                lineWrapping: true,
                mode: 'text/html',
                htmlMode: true,
                theme: 'mdn-like',
                tabMode: 'indent',
                tabSize: 2,
                extraKeys: { "Ctrl-Space": "autocomplete" }
            },
            cleaner: {
                action: 'paste', // both|button|paste 'button' only cleans via toolbar button, 'paste' only clean when pasting content, both does both options.
                newline: '<br>', // Summernote's default is to use '<p><br></p>'
                notStyle: 'position:absolute;top:0;left:0;right:0', // Position of Notification
                icon: '<i class="note-icon">[Your Button]</i>',
                keepHtml: true, // Remove all Html formats
                keepOnlyTags: ['<p>', '<br>', '<ul>', '<li>', '<b>', '<strong>', '<i>', '<a>', '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>'], // If keepHtml is true, remove all tags except these
                keepClasses: false, // Remove Classes
                badTags: ['style', 'script', 'applet', 'embed', 'noframes', 'noscript', 'html'], // Remove full tags with contents
                badAttributes: ['style', 'start'], // Remove attributes from remaining tags
                limitChars: false, // 0/false|# 0/false disables option
                limitDisplay: 'both', // text|html|both
                limitStop: false // true/false
            }
        };

        const { nameField, placeholder, editorRef, options, textHeight, minHeight, required, requiredField, value, defaultValue, onChange, column, previewHide, content, ...props } = this.props;

        if (textHeight) { defaults['height'] = textHeight; }
        if (minHeight) { defaults['minHeight'] = minHeight; }

        const settings = { ...defaults, ...options };

        return (
            <div className={`editor-content ${this.props.className}`} ref={node => { this.nodeEditor = node; }}>
                <div className="row">
                    <div className={column ? column : "col-md-6"}>
                        <ReactSummernote
                            value={this.state.value}
                            options={settings}
                            onChange={this.handleChange}
                            onImageUpload={this.imageUpload}
                            ref={(el) => { this.refEditor(el); if (typeof editorRef === 'function') editorRef(el); }}
                            {...props}
                        />
                        <input
                            type="hidden"
                            name={nameField}
                            value={this.state.value}
                            required={required || requiredField}
                        />
                    </div>
                    {previewHide ? '' :
                        <div className="col-md-6">
                            <div className="editor-content-preview x_panel" dangerouslySetInnerHTML={{ __html: this.state.value }} />
                        </div>
                    }
                </div>
            </div>
        );
    }
}

ProjectEditor.propTypes = {
    formCreate: PropTypes.any.isRequired,
    className: PropTypes.string,
    content: PropTypes.instanceOf(Backbone.Model),
    nameField: PropTypes.string,
    defaultValue: PropTypes.string,
    value: PropTypes.string,
    placeholder: PropTypes.string,
    required: PropTypes.bool,
    requiredField: PropTypes.bool,
    options: PropTypes.object,
    onChange: PropTypes.func,
    editorRef: PropTypes.func
};

ProjectEditor.defaultProps = {
    formCreate: '',
    nameField: '',
    className: '',
    placeholder: '',
    required: false,
    requiredField: false,
    options: {},
    content: new Backbone.Model,
    previewHide: false,
    editorRef: () => { }
};

export default ProjectEditor;
