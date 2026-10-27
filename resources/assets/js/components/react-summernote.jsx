import React, { Component } from 'react';
import PropTypes from 'prop-types';

const randomUid = () => Math.floor(Math.random() * 100000);

class ReactSummernote extends Component {
  constructor(props) {
    super(props);

    this.uid = `react-summernote-${randomUid()}`;
    this.editor = null;
    this.noteEditable = null;
    this.notePlaceholder = null;

    this.onInit = this.onInit.bind(this);
    this.onImageUpload = this.onImageUpload.bind(this);

    this.focus = this.focus.bind(this);
    this.isEmpty = this.isEmpty.bind(this);
    this.reset = this.reset.bind(this);
    this.replace = this.replace.bind(this);
    this.disable = this.disable.bind(this);
    this.enable = this.enable.bind(this);
    this.toggleState = this.toggleState.bind(this);
    this.insertImage = this.insertImage.bind(this);
    this.insertNode = this.insertNode.bind(this);
    this.insertText = this.insertText.bind(this);
  }

  componentDidMount() {
    const $ = window.jQuery || window.$;
    if (!$ || typeof $.fn.summernote !== 'function') {
      console.warn('Summernote jQuery plugin not yet available');
      return;
    }

    const options = { ...(this.props.options || {}) };
    const codeview = this.props.codeview;
    options.callbacks = this.callbacks;

    this.editor = $(`#${this.uid}`);
    this.editor.summernote(options);

    const $container = this.editor.parent();
    this.noteEditable = $container.find('.note-editable');
    this.notePlaceholder = $container.find('.note-placeholder');

    if (this.props.value) {
      this.editor.summernote('code', this.props.value);
    }

    if (codeview) {
      this.editor.summernote('codeview.activate');
    }
  }

  UNSAFE_componentWillReceiveProps(nextProps) {
    const { props } = this;
    const codeview = nextProps.codeview;
    const codeviewCommand = codeview ? 'codeview.activate' : 'codeview.deactivate';

    if (typeof nextProps.value === 'string' && props.value !== nextProps.value) {
      if (this.editor && typeof this.editor.summernote === 'function') {
        const currentCode = this.editor.summernote('code');
        const isBothEmpty = (!nextProps.value || nextProps.value === '') && this.isEmpty();
        const hasFocus = this.noteEditable && (this.noteEditable.is(':focus') || this.noteEditable.find(':focus').length > 0);

        if (!hasFocus && currentCode !== nextProps.value && !isBothEmpty) {
          this.replace(nextProps.value);
        }
      }
    }

    if (typeof nextProps.disabled === 'boolean' && props.disabled !== nextProps.disabled) {
      this.toggleState(nextProps.disabled);
    }
    if (codeview !== props.codeview && this.editor && this.editor.summernote) {
      this.editor.summernote(codeviewCommand);
    }
  }

  shouldComponentUpdate() {
    return false;
  }

  componentWillUnmount() {
    if (this.editor && typeof this.editor.summernote === 'function') {
      try {
        this.editor.summernote('destroy');
      } catch (e) {}
    }
  }

  onInit() {
    const { disabled, onInit } = this.props;

    if (this.editor) {
      const $container = this.editor.parent();
      this.noteEditable = $container.find('.note-editable');
      this.notePlaceholder = $container.find('.note-placeholder');
    }

    if (typeof disabled === 'boolean') {
      this.toggleState(disabled);
    }

    if (typeof onInit === 'function') {
      onInit({
        noteEditable: this.noteEditable,
        notePlaceholder: this.notePlaceholder
      });
    }
  }

  onImageUpload(files) {
    const { onImageUpload } = this.props;
    if (typeof onImageUpload === 'function') {
      onImageUpload(files);
    }
  }

  focus() {
    if (this.editor && this.editor.summernote) {
      this.editor.summernote('focus');
    }
  }

  isEmpty() {
    if (this.editor && this.editor.summernote) {
      return this.editor.summernote('isEmpty');
    }
    return true;
  }

  reset() {
    if (this.editor && this.editor.summernote) {
      this.editor.summernote('reset');
    }
  }

  replace(content) {
    if (this.editor && this.editor.summernote) {
      this.editor.summernote('code', content || '');
      if (this.notePlaceholder) {
        if (this.isEmpty()) {
          this.notePlaceholder.show();
        } else {
          this.notePlaceholder.hide();
        }
      }
    }
  }

  disable() {
    if (this.editor && this.editor.summernote) {
      this.editor.summernote('disable');
    }
  }

  enable() {
    if (this.editor && this.editor.summernote) {
      this.editor.summernote('enable');
    }
  }

  toggleState(disabled) {
    if (disabled) {
      this.disable();
    } else {
      this.enable();
    }
  }

  insertImage(url, filenameOrCallback) {
    if (this.editor && this.editor.summernote) {
      this.editor.summernote('insertImage', url, filenameOrCallback);
    }
  }

  insertNode(node) {
    if (this.editor && this.editor.summernote) {
      this.editor.summernote('insertNode', node);
    }
  }

  insertText(text) {
    if (this.editor && this.editor.summernote) {
      this.editor.summernote('insertText', text);
    }
  }

  get callbacks() {
    const props = this.props;

    return {
      onInit: this.onInit,
      onEnter: props.onEnter,
      onFocus: props.onFocus,
      onBlur: props.onBlur,
      onKeyup: props.onKeyUp,
      onKeydown: props.onKeyDown,
      onPaste: props.onPaste,
      onChange: props.onChange,
      onImageUpload: this.onImageUpload
    };
  }

  render() {
    const { tag: Tag = 'div', children, className } = this.props;

    return (
      <div className={className || ''}>
        <Tag id={this.uid}>{children}</Tag>
      </div>
    );
  }
}

ReactSummernote.propTypes = {
  value: PropTypes.string,
  options: PropTypes.object,
  codeview: PropTypes.bool,
  disabled: PropTypes.bool,
  onInit: PropTypes.func,
  onImageUpload: PropTypes.func,
  onChange: PropTypes.func,
  onEnter: PropTypes.func,
  onFocus: PropTypes.func,
  onBlur: PropTypes.func,
  onKeyUp: PropTypes.func,
  onKeyDown: PropTypes.func,
  onPaste: PropTypes.func,
  tag: PropTypes.string,
  className: PropTypes.string
};

ReactSummernote.defaultProps = {
  tag: 'div',
  className: '',
  options: {},
  codeview: false,
  disabled: false
};

export default ReactSummernote;
