(function($) {

    var Dropzone = {}, detectVerticalSquash, drawImageIOSFix, noop;
    noop = function() {
    };

    function _Dropzone(element, options) {
	console.log('init');
	this.element = element;
	if (typeof this.element === "string")
	    this.element = document.querySelector(this.element);
	if (!(this.element && (this.element.nodeType != null)))
	    throw new Error("Invalid dropzone element.");
	if (this.element.dropzone)
	    throw new Error("Dropzone already attached.");

	this.version = Dropzone.version;
	this.defaultOptions.previewTemplate = this.defaultOptions.previewTemplate.replace(/\n*/g, "");
	this.clickableElements = [];
	this.listeners = [];
	this.files = [];

	Dropzone.instances.push(this);
	this.element.dropzone = this;

	$.event.props.push("dataTransfer");

	this.options = $.extend({}, this.defaultOptions, options != null ? options : {});

	if (this.options.url == null)
	    this.options.url = this.element.getAttribute("action");
	if (!this.options.url)
	    throw new Error("No URL provided.");
	if (this.options.acceptedFiles && this.options.acceptedMimeTypes)
	    throw new Error("You can't provide both 'acceptedFiles' and 'acceptedMimeTypes'. 'acceptedMimeTypes' is deprecated.");
	if (this.options.acceptedMimeTypes) {
	    this.options.acceptedFiles = this.options.acceptedMimeTypes;
	    delete this.options.acceptedMimeTypes;
	}
	this.options.method = this.options.method.toUpperCase();

	if (this.options.previewsContainer) {
	    this.previewsContainer = Dropzone.getElement(this.options.previewsContainer, "previewsContainer");
	} else {
	    this.previewsContainer = this.element;
	}
	if (this.options.clickable) {
	    if (this.options.clickable === true) {
		this.clickableElements = [ this.element ];
	    } else {
		this.clickableElements = Dropzone.getElements(this.options.clickable, "clickable");
	    }
	}
	this.init();
	console.log('End');
    }
    _Dropzone.prototype = {
	emit : function(event) {
	    console.log(event);
	    this._callbacks = this._callbacks || {};
	    console.log(this._callbacks);
	    var args = [].slice.call(arguments, 1), callbacks = this._callbacks[event];
	    console.log(callbacks);
	    if (callbacks) {
		for (var i = 0, len = callbacks.length; i < len; ++i) {
		    if (typeof callbacks[i] === 'function')
			callbacks[i].apply(this, args);
		}
	    }
	    return this;
	},
	on : function(event, fn) {
	    this._callbacks = this._callbacks || {};
	    (this._callbacks[event] = this._callbacks[event] || []).push(fn);
	    return this;
	},
	events : [ "drop", "dragstart", "dragend", "dragenter", "dragover", "dragleave", "addedfile", "removedfile", "thumbnail", "error", "errormultiple", "processing", "processingmultiple", "uploadprogress", "totaluploadprogress", "sending", "sendingmultiple", "success", "successmultiple", "canceled", "canceledmultiple", "complete", "completemultiple", "reset", "maxfilesexceeded", "maxfilesreached" ],
	defaultOptions : {
	    url : null,
	    method : "post",
	    withCredentials : false,
	    parallelUploads : 2,
	    uploadMultiple : false,
	    maxFilesize : 256,
	    paramName : "file",
	    createImageThumbnails : true,
	    maxThumbnailFilesize : 10,
	    thumbnailWidth : 100,
	    thumbnailHeight : 100,
	    maxFiles : null,
	    params : {},
	    clickable : true,
	    ignoreHiddenFiles : true,
	    acceptedFiles : null,
	    acceptedMimeTypes : null,
	    autoProcessQueue : true,
	    addRemoveLinks : false,
	    previewsContainer : null,
	    dictDefaultMessage : "Drop files here to upload",
	    dictFallbackMessage : "Your browser does not support drag'n'drop file uploads.",
	    dictFallbackText : "Please use the fallback form below to upload your files like in the olden days.",
	    dictFileTooBig : "File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.",
	    dictInvalidFileType : "You can't upload files of this type.",
	    dictResponseError : "Server responded with {{statusCode}} code.",
	    dictCancelUpload : "Cancel upload",
	    dictCancelUploadConfirmation : "Are you sure you want to cancel this upload?",
	    dictRemoveFile : "Remove file",
	    dictRemoveFileConfirmation : null,
	    dictMaxFilesExceeded : "You can not upload any more files.",
	    accept : function(file, done) {
		return done();
	    },
	    init : function() {
		return noop;
	    },
	    resize : function(file) {
		var info, srcRatio, trgRatio;
		info = {
		    srcX : 0,
		    srcY : 0,
		    srcWidth : file.width,
		    srcHeight : file.height
		};
		srcRatio = file.width / file.height;
		trgRatio = this.options.thumbnailWidth / this.options.thumbnailHeight;
		if (file.height < this.options.thumbnailHeight || file.width < this.options.thumbnailWidth) {
		    info.trgHeight = info.srcHeight;
		    info.trgWidth = info.srcWidth;
		} else {
		    if (srcRatio > trgRatio) {
			info.srcHeight = file.height;
			info.srcWidth = info.srcHeight * trgRatio;
		    } else {
			info.srcWidth = file.width;
			info.srcHeight = info.srcWidth / trgRatio;
		    }
		}
		info.srcX = (file.width - info.srcWidth) / 2;
		info.srcY = (file.height - info.srcHeight) / 2;
		return info;
	    },
	    drop : function(e) {
		return this.element.classList.remove("dz-drag-hover");
	    },
	    dragstart : noop(),
	    dragend : function(e) {
		return this.element.classList.remove("dz-drag-hover");
	    },
	    dragenter : function(e) {
		return this.element.classList.add("dz-drag-hover");
	    },
	    dragover : function(e) {
		return this.element.classList.add("dz-drag-hover");
	    },
	    dragleave : function(e) {
		return this.element.classList.remove("dz-drag-hover");
	    },
	    paste : noop(),
	    reset : function() {
		return this.element.classList.remove("dz-started");
	    },
	    addedfile : function(file) {
		var node, removeFileEvent, removeLink, _i, _j, _k, _len, _len1, _len2, _ref, _ref1, _ref2, _results;
		if (this.element === this.previewsContainer) {
		    this.element.classList.add("dz-started");
		}
		file.previewElement = Dropzone.createElement(this.options.previewTemplate.trim());
		file.previewTemplate = file.previewElement;
		this.previewsContainer.appendChild(file.previewElement);
		_ref = file.previewElement.querySelectorAll("[data-dz-name]");
		for (_i = 0, _len = _ref.length; _i < _len; _i++) {
		    node = _ref[_i];
		    node.textContent = file.name;
		}
		_ref1 = file.previewElement.querySelectorAll("[data-dz-size]");
		for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
		    node = _ref1[_j];
		    node.innerHTML = this.filesize(file.size);
		}
		if (this.options.addRemoveLinks) {
		    file._removeLink = Dropzone.createElement("<a class=\"dz-remove\" href=\"javascript:undefined;\" data-dz-remove>" + this.options.dictRemoveFile + "</a>");
		    file.previewElement.appendChild(file._removeLink);
		}
		removeFileEvent = (function(_this) {
		    return function(e) {
			e.preventDefault();
			e.stopPropagation();
			if (file.status === Dropzone.UPLOADING) {
			    return Dropzone.confirm(_this.options.dictCancelUploadConfirmation, function() {
				return _this.removeFile(file);
			    });
			} else {
			    if (_this.options.dictRemoveFileConfirmation) {
				return Dropzone.confirm(_this.options.dictRemoveFileConfirmation, function() {
				    return _this.removeFile(file);
				});
			    } else {
				return _this.removeFile(file);
			    }
			}
		    };
		})(this);
		_ref2 = file.previewElement.querySelectorAll("[data-dz-remove]");
		_results = [];
		for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
		    removeLink = _ref2[_k];
		    _results.push(removeLink.addEventListener("click", removeFileEvent));
		}
		return _results;
	    },
	    removedfile : function(file) {
		var _ref;
		if ((_ref = file.previewElement) != null) {
		    _ref.parentNode.removeChild(file.previewElement);
		}
		return this._updateMaxFilesReachedClass();
	    },
	    thumbnail : function(file, dataUrl) {
		var thumbnailElement, _i, _len, _ref, _results;
		file.previewElement.classList.remove("dz-file-preview");
		file.previewElement.classList.add("dz-image-preview");
		_ref = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
		_results = [];
		for (_i = 0, _len = _ref.length; _i < _len; _i++) {
		    thumbnailElement = _ref[_i];
		    thumbnailElement.alt = file.name;
		    _results.push(thumbnailElement.src = dataUrl);
		}
		return _results;
	    },
	    error : function(file, message) {
		var node, _i, _len, _ref, _results;
		file.previewElement.classList.add("dz-error");
		if (typeof message !== "String" && message.error) {
		    message = message.error;
		}
		_ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
		_results = [];
		for (_i = 0, _len = _ref.length; _i < _len; _i++) {
		    node = _ref[_i];
		    _results.push(node.textContent = message);
		}
		return _results;
	    },
	    errormultiple : noop(),
	    processing : function(file) {
		file.previewElement.classList.add("dz-processing");
		if (file._removeLink) {
		    return file._removeLink.textContent = this.options.dictCancelUpload;
		}
	    },
	    processingmultiple : noop(),
	    uploadprogress : function(file, progress, bytesSent) {
		var node, _i, _len, _ref, _results;
		_ref = file.previewElement.querySelectorAll("[data-dz-uploadprogress]");
		_results = [];
		for (_i = 0, _len = _ref.length; _i < _len; _i++) {
		    node = _ref[_i];
		    _results.push(node.style.width = "" + progress + "%");
		}
		return _results;
	    },
	    totaluploadprogress : noop(),
	    sending : noop(),
	    sendingmultiple : noop(),
	    success : function(file) {
		return file.previewElement.classList.add("dz-success");
	    },
	    successmultiple : noop(),
	    canceled : function(file) {
		return this.emit("error", file, "Upload canceled.");
	    },
	    canceledmultiple : noop(),
	    complete : function(file) {
		if (file._removeLink) {
		    return file._removeLink.textContent = this.options.dictRemoveFile;
		}
	    },
	    completemultiple : noop(),
	    maxfilesexceeded : noop(),
	    maxfilesreached : noop(),
	    previewTemplate : "<div class=\"dz-preview dz-file-preview\">\n <div class=\"dz-details\">\n <div class=\"dz-filename\"><span data-dz-name></span></div>\n <div class=\"dz-size\" data-dz-size></div>\n <img data-dz-thumbnail />\n </div>\n <div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>\n <div class=\"dz-success-mark\"><span>✔</span></div>\n <div class=\"dz-error-mark\"><span>✘</span></div>\n <div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n</div>"
	},
	getAcceptedFiles : function() { // done
	    var _results = [];
	    $.each(this.files, function(key, file) {
		if (file.accepted)
		    _results.push(file);
	    });
	    return _results;
	},
	getRejectedFiles : function() { // done
	    var _results = [];
	    $.each(this.files, function(key, file) {
		if (!file.accepted)
		    _results.push(file);
	    });
	    return _results;
	},
	getQueuedFiles : function() { // done
	    var _results = [];
	    $.each(this.files, function(key, file) {
		if (file.status === Dropzone.QUEUED)
		    _results.push(file);
	    });
	    return _results;
	},
	getUploadingFiles : function() { // done
	    var _results = [];
	    $.each(this.files, function(key, file) {
		if (file.status === Dropzone.UPLOADING)
		    _results.push(file);
	    });
	    return _results;
	},
	init : function() { // done
	    var self = this, noPropagation, setupHiddenFileInput;
	    if (this.element.tagName === "FORM" || this.element.tagName === "form")
		$(this.element).prop("enctype", "multipart/form-data");
	    if (this.element.classList.contains("dropzone") && !this.element.querySelector(".dz-message"))
		$(this.element).append("<div class=\"dz-default dz-message\"><span>" + this.options.dictDefaultMessage + "</span></div>");
	    if (this.clickableElements.length) {
		setupHiddenFileInput = function() {
		    if (self.hiddenFileInput)
			$(self.hiddenFileInput).remove();
		    self.hiddenFileInput = $("<input>").prop("type", "file").addClass("dz-hidden-input");
		    if (self.options.maxFiles == null || self.options.maxFiles > 1)
			self.hiddenFileInput.prop("multiple", "multiple");
		    if (self.options.acceptedFiles != null)
			self.hiddenFileInput.prop("accept", self.options.acceptedFiles);
		    $(self.element).append(self.hiddenFileInput);
		    return $(self.hiddenFileInput).on("change", function() {
			$.each(self.hiddenFileInput[0].files, function(key, file) {
			    self.addFile(file);
			});
			return setupHiddenFileInput.call(self);
		    });
		};
		setupHiddenFileInput();
	    }
	    this.URL = (window.URL != null) ? window.URL : window.webkitURL;
	    $.each(this.events, function(key, eventName) {
		self.on(eventName, self.options[eventName]);
	    });
	    this.on("uploadprogress", function() {
		console.log('arkium - uploadprogress');
		return self.updateTotalUploadProgress();
	    });
	    this.on("removedfile", function() {
		console.log('arkium - removedfile');
		return self.updateTotalUploadProgress();
	    });
	    this.on("canceled", function(file) {
		console.log('arkium - canceled');
		return self.emit("complete", file);
	    });
	    this.on("complete", function(file) {
		console.log('arkium - complete');
		if (self.getUploadingFiles().length === 0 && self.getQueuedFiles().length === 0) {
		    return setTimeout((function() {
			return self.emit("queuecomplete");
		    }), 0);
		}
	    });
	    noPropagation = function(e) {
		e.stopPropagation();
		if (e.preventDefault) {
		    return e.preventDefault();
		} else {
		    return e.returnValue = false;
		}
	    };
	    this.listeners = [ {
		element : this.element,
		events : {
		    "dragstart" : function(e) {
			console.log('arkium - dragstart');
			return self.emit("dragstart", e);
		    },
		    "dragenter" : function(e) {
			console.log('arkium - dragenter');
			noPropagation(e);
			return self.emit("dragenter", e);
		    },
		    "dragover" : function(e) {
			console.log('arkium - dragover');
			try {
			    var efct = e.dataTransfer.effectAllowed;
			} catch (e) {
			    console.log(e);
			}
			e.dataTransfer.dropEffect = ('move' === efct || 'linkMove' === efct) ? 'move' : 'copy';
			noPropagation(e);
			return self.emit("dragover", e);
		    },
		    "dragleave" : function(e) {
			console.log('arkium - dragleave');
			return self.emit("dragleave", e);
		    },
		    "drop" : function(e) {
			console.log('arkium - drop');
			noPropagation(e);
			return self.drop(e);
		    },
		    "dragend" : function(e) {
			console.log('arkium - dragend');
			return _this.emit("dragend", e);
		    }
		}
	    } ];
	    $.each(this.clickableElements, function(key, clickableElement) {
		self.listeners.push({
		    element : clickableElement,
		    events : {
			"click" : function(evt) {
			    console.log('arkium - click Element');
			    if ((clickableElement !== self.element) || (evt.target === self.element || Dropzone.elementInside(evt.target, self.element.querySelector(".dz-message")))) {
				return self.hiddenFileInput.click();
			    }
			}
		    }
		});
	    });
	    this.enable();
	    return this.options.init.call(this);
	},
	destroy : function() { // Encore à contrôler
	    var _ref;
	    this.disable();
	    this.removeAllFiles(true);
	    $(this.element).empty();
	    // if ((_ref = this.hiddenFileInput) != null ? _ref.parentNode :
	    // void 0) {
	    // this.hiddenFileInput.parentNode.removeChild(this.hiddenFileInput);
	    // this.hiddenFileInput = null;
	    // }
	    delete this.hiddenFileInput;
	    delete this.element.dropzone;
	    return this.element.dropzone;
	    // return
	    // Dropzone.instances.splice(Dropzone.instances.indexOf(this), 1);
	},
	updateTotalUploadProgress : function() {
	    var acceptedFiles, file, totalBytes, totalBytesSent, totalUploadProgress, _i, _len, _ref;
	    totalBytesSent = 0;
	    totalBytes = 0;
	    acceptedFiles = this.getAcceptedFiles();
	    if (acceptedFiles.length) {
		_ref = this.getAcceptedFiles();
		for (_i = 0, _len = _ref.length; _i < _len; _i++) {
		    file = _ref[_i];
		    totalBytesSent += file.upload.bytesSent;
		    totalBytes += file.upload.total;
		}
		totalUploadProgress = 100 * totalBytesSent / totalBytes;
	    } else {
		totalUploadProgress = 100;
	    }
	    return this.emit("totaluploadprogress", totalUploadProgress, totalBytes, totalBytesSent);
	},
	setupEventListeners : function() { // done
	    console.log('arkium - setupEventListeners');
	    var _results = [];
	    $.each(this.listeners, function(key, elementListeners) {
		var _results1 = [];
		$.each(elementListeners.events, function(event, listener) {
		    _results1.push($(elementListeners.element).on(event, listener));
		});
		_results.push(_results1);
	    });
	    return _results;
	},
	removeEventListeners : function() { // done
	    console.log('arkium - removeEventListeners');
	    var _results = [];
	    $.each(this.listeners, function(key, elementListeners) {
		var _results1 = [];
		$.each(elementListeners.events, function(event, listener) {
		    _results1.push($(elementListeners.element).off(event, listener));
		});
		_results.push(_results1);
	    });
	    return _results;
	},
	disable : function() { // done
	    console.log('arkium - disable');
	    var _results = [], self = this;
	    $.each(this.clickableElements, function(key, element) {
		return $(element).removeClass("dz-clickable");
	    });
	    this.removeEventListeners();
	    $.each(this.files, function(key, file) {
		_results.push(self.cancelUpload(file));
	    });
	    return _results;
	},
	enable : function() { // done
	    console.log('arkium - enable');
	    $.each(this.clickableElements, function(key, element) {
		return $(element).addClass("dz-clickable");
	    });
	    return this.setupEventListeners();
	},
	filesize : function(size) { // done
	    var string;
	    if (size >= 1024 * 1024 * 1024 * 1024 / 10) {
		size = size / (1024 * 1024 * 1024 * 1024 / 10);
		string = "TiB";
	    } else if (size >= 1024 * 1024 * 1024 / 10) {
		size = size / (1024 * 1024 * 1024 / 10);
		string = "GiB";
	    } else if (size >= 1024 * 1024 / 10) {
		size = size / (1024 * 1024 / 10);
		string = "MiB";
	    } else if (size >= 1024 / 10) {
		size = size / (1024 / 10);
		string = "KiB";
	    } else {
		size = size * 10;
		string = "b";
	    }
	    return "<strong>" + (Math.round(size) / 10) + "</strong> " + string;
	},
	_updateMaxFilesReachedClass : function() {
	    if ((this.options.maxFiles != null) && this.getAcceptedFiles().length >= this.options.maxFiles) {
		if (this.getAcceptedFiles().length === this.options.maxFiles) {
		    this.emit('maxfilesreached', this.files);
		}
		return this.element.classList.add("dz-max-files-reached");
	    } else {
		return this.element.classList.remove("dz-max-files-reached");
	    }
	},
	drop : function(e) { // done
	    if (!e.dataTransfer)
		return;
	    this.emit("drop", e);
	    var files = e.dataTransfer.files;
	    if (files.length) {
		var items = files.items;
		if (items && items.length && (items[0].webkitGetAsEntry != null)) {
		    console.log('1');
		    this._addFilesFromItems(items);
		} else {
		    this.handleFiles(files);
		}
	    }
	},
	paste : function(e) { // done ??
	    console.log('arkium - paste');
	    if ((e != null ? e.clipboardData != null ? e.clipboardData.items : void 0 : void 0) == null)
		return;
	    this.emit("paste", e);
	    var items = e.clipboardData.items;
	    if (items.length)
		return this._addFilesFromItems(items);
	},
	handleFiles : function(files) { // done
	    var _results = [], self = this;
	    $.each(files, function(key, file) {
		_results.push(self.addFile(file));
	    });
	    return _results;
	},
	_addFilesFromItems : function(items) {
	    var entry, item, _i, _len, _results;
	    _results = [];
	    for (_i = 0, _len = items.length; _i < _len; _i++) {
		item = items[_i];
		if ((item.webkitGetAsEntry != null) && (entry = item.webkitGetAsEntry())) {
		    if (entry.isFile) {
			_results.push(this.addFile(item.getAsFile()));
		    } else if (entry.isDirectory) {
			_results.push(this._addFilesFromDirectory(entry, entry.name));
		    } else {
			_results.push(void 0);
		    }
		} else if (item.getAsFile != null) {
		    if ((item.kind == null) || item.kind === "file") {
			_results.push(this.addFile(item.getAsFile()));
		    } else {
			_results.push(void 0);
		    }
		} else {
		    _results.push(void 0);
		}
	    }
	    return _results;
	},
	_addFilesFromDirectory : function(directory, path) {
	    var dirReader, entriesReader;
	    dirReader = directory.createReader();
	    entriesReader = (function(_this) {
		return function(entries) {
		    var entry, _i, _len;
		    for (_i = 0, _len = entries.length; _i < _len; _i++) {
			entry = entries[_i];
			if (entry.isFile) {
			    entry.file(function(file) {
				if (_this.options.ignoreHiddenFiles && file.name.substring(0, 1) === '.') {
				    return;
				}
				file.fullPath = "" + path + "/" + file.name;
				return _this.addFile(file);
			    });
			} else if (entry.isDirectory) {
			    _this._addFilesFromDirectory(entry, "" + path + "/" + entry.name);
			}
		    }
		};
	    })(this);
	    return dirReader.readEntries(entriesReader, function(error) {
		return typeof console !== "undefined" && console !== null ? typeof console.log === "function" ? console.log(error) : void 0 : void 0;
	    });
	},
	accept : function(file, done) {
	    if (file.size > this.options.maxFilesize * 1024 * 1024) {
		return done(this.options.dictFileTooBig.replace("{{filesize}}", Math.round(file.size / 1024 / 10.24) / 100).replace("{{maxFilesize}}", this.options.maxFilesize));
	    } else if (!Dropzone.isValidFile(file, this.options.acceptedFiles)) {
		return done(this.options.dictInvalidFileType);
	    } else if ((this.options.maxFiles != null) && this.getAcceptedFiles().length >= this.options.maxFiles) {
		done(this.options.dictMaxFilesExceeded.replace("{{maxFiles}}", this.options.maxFiles));
		return this.emit("maxfilesexceeded", file);
	    } else {
		return this.options.accept.call(this, file, done);
	    }
	},
	addFile : function(file) {
	    file.upload = {
		progress : 0,
		total : file.size,
		bytesSent : 0
	    };
	    this.files.push(file);
	    file.status = Dropzone.ADDED;
	    this.emit("addedfile", file);
	    this._enqueueThumbnail(file);
	    return this.accept(file, (function(_this) {
		return function(error) {
		    if (error) {
			file.accepted = false;
			_this._errorProcessing([ file ], error);
		    } else {
			_this.enqueueFile(file);
		    }
		    return _this._updateMaxFilesReachedClass();
		};
	    })(this));
	},
	enqueueFiles : function(files) {
	    var file, _i, _len;
	    for (_i = 0, _len = files.length; _i < _len; _i++) {
		file = files[_i];
		this.enqueueFile(file);
	    }
	    return null;
	},
	enqueueFile : function(file) {
	    file.accepted = true;
	    if (file.status === Dropzone.ADDED) {
		file.status = Dropzone.QUEUED;
		if (this.options.autoProcessQueue) {
		    return setTimeout(((function(_this) {
			return function() {
			    return _this.processQueue();
			};
		    })(this)), 0);
		}
	    } else {
		throw new Error("This file can't be queued because it has already been processed or was rejected.");
	    }
	},
	_thumbnailQueue : [], // done
	_processingThumbnail : false, // done
	_enqueueThumbnail : function(file) {
	    if (this.options.createImageThumbnails && file.type.match(/image.*/) && file.size <= this.options.maxThumbnailFilesize * 1024 * 1024) {
		this._thumbnailQueue.push(file);
		return setTimeout(((function(_this) {
		    return function() {
			return _this._processThumbnailQueue();
		    };
		})(this)), 0);
	    }
	},
	_processThumbnailQueue : function() {
	    if (this._processingThumbnail || this._thumbnailQueue.length === 0) {
		return;
	    }
	    this._processingThumbnail = true;
	    return this.createThumbnail(this._thumbnailQueue.shift(), (function(_this) {
		return function() {
		    _this._processingThumbnail = false;
		    return _this._processThumbnailQueue();
		};
	    })(this));
	},
	removeFile : function(file) { // done
	    console.log('arkium - removeFile');
	    var without = function(list, rejectedItem) {
		var _results = [];
		$.each(list, function(key, item) {
		    if (item !== rejectedItem)
			_results.push(item);
		});
		return _results;
	    };
	    if (file.status === Dropzone.UPLOADING)
		this.cancelUpload(file);
	    this.files = without(this.files, file);
	    this.emit("removedfile", file);
	    if (this.files.length === 0)
		return this.emit("reset");
	},
	removeAllFiles : function(cancelIfNecessary) { // done
	    console.log('arkium - removeAllFiles');
	    var self = this;
	    if (cancelIfNecessary == null)
		cancelIfNecessary = false;
	    $.each(this.files.slice(), function(key, file) {
		if (file.status !== Dropzone.UPLOADING || cancelIfNecessary)
		    console.log(file);
		self.removeFile(file);
	    });
	    return null;
	},
	createThumbnail : function(file, callback) {
	    var fileReader;
	    fileReader = new FileReader;
	    fileReader.onload = (function(_this) {
		return function() {
		    var img;
		    img = document.createElement("img");
		    img.onload = function() {
			var canvas, ctx, resizeInfo, thumbnail, _ref, _ref1, _ref2, _ref3;
			file.width = img.width;
			file.height = img.height;
			resizeInfo = _this.options.resize.call(_this, file);
			if (resizeInfo.trgWidth == null) {
			    resizeInfo.trgWidth = _this.options.thumbnailWidth;
			}
			if (resizeInfo.trgHeight == null) {
			    resizeInfo.trgHeight = _this.options.thumbnailHeight;
			}
			canvas = document.createElement("canvas");
			ctx = canvas.getContext("2d");
			canvas.width = resizeInfo.trgWidth;
			canvas.height = resizeInfo.trgHeight;
			drawImageIOSFix(ctx, img, (_ref = resizeInfo.srcX) != null ? _ref : 0, (_ref1 = resizeInfo.srcY) != null ? _ref1 : 0, resizeInfo.srcWidth, resizeInfo.srcHeight, (_ref2 = resizeInfo.trgX) != null ? _ref2 : 0, (_ref3 = resizeInfo.trgY) != null ? _ref3 : 0, resizeInfo.trgWidth, resizeInfo.trgHeight);
			thumbnail = canvas.toDataURL("image/png");
			_this.emit("thumbnail", file, thumbnail);
			if (callback != null) {
			    return callback();
			}
		    };
		    return img.src = fileReader.result;
		};
	    })(this);
	    return fileReader.readAsDataURL(file);
	},
	processQueue : function() {
	    var i, parallelUploads, processingLength, queuedFiles;
	    parallelUploads = this.options.parallelUploads;
	    processingLength = this.getUploadingFiles().length;
	    i = processingLength;
	    if (processingLength >= parallelUploads)
		return;
	    queuedFiles = this.getQueuedFiles();
	    if (!(queuedFiles.length > 0))
		return;
	    if (this.options.uploadMultiple) {
		return this.processFiles(queuedFiles.slice(0, parallelUploads - processingLength));
	    } else {
		while (i < parallelUploads) {
		    if (!queuedFiles.length) {
			return;
		    }
		    this.processFile(queuedFiles.shift());
		    i++;
		}
	    }
	},
	processFile : function(file) { // done
	    return this.processFiles([ file ]);
	},
	processFiles : function(files) { // done
	    var self = this;
	    $.each(files, function(key, file) {
		file.processing = true;
		file.status = Dropzone.UPLOADING;
		self.emit("processing", file);
	    });
	    if (this.options.uploadMultiple)
		this.emit("processingmultiple", files);
	    return this.uploadFiles(files);
	},
	_getFilesWithXhr : function(xhr) {
	    var file, files;
	    return files = (function() {
		var _i, _len, _ref, _results;
		_ref = this.files;
		_results = [];
		for (_i = 0, _len = _ref.length; _i < _len; _i++) {
		    file = _ref[_i];
		    if (file.xhr === xhr) {
			_results.push(file);
		    }
		}
		return _results;
	    }).call(this);
	},
	cancelUpload : function(file) {
	    var groupedFile, groupedFiles, _i, _j, _len, _len1, _ref;
	    if (file.status === Dropzone.UPLOADING) {
		groupedFiles = this._getFilesWithXhr(file.xhr);
		for (_i = 0, _len = groupedFiles.length; _i < _len; _i++) {
		    groupedFile = groupedFiles[_i];
		    groupedFile.status = Dropzone.CANCELED;
		}
		file.xhr.abort();
		for (_j = 0, _len1 = groupedFiles.length; _j < _len1; _j++) {
		    groupedFile = groupedFiles[_j];
		    this.emit("canceled", groupedFile);
		}
		if (this.options.uploadMultiple) {
		    this.emit("canceledmultiple", groupedFiles);
		}
	    } else if ((_ref = file.status) === Dropzone.ADDED || _ref === Dropzone.QUEUED) {
		file.status = Dropzone.CANCELED;
		this.emit("canceled", file);
		if (this.options.uploadMultiple) {
		    this.emit("canceledmultiple", [ file ]);
		}
	    }
	    if (this.options.autoProcessQueue) {
		return this.processQueue();
	    }
	},
	uploadFile : function(file) { // done
	    return this.uploadFiles([ file ]);
	},
	uploadFiles : function(files) {
	    var file, formData, handleError, headerName, headerValue, headers, input, inputName, inputType, key, option, progressObj, response, updateProgress, value, xhr, _i, _j, _k, _l, _len, _len1, _len2, _len3, _len4, _m, _ref, _ref1, _ref2, _ref3, _ref4;
	    xhr = new XMLHttpRequest();
	    for (_i = 0, _len = files.length; _i < _len; _i++) {
		file = files[_i];
		file.xhr = xhr;
	    }
	    xhr.open(this.options.method, this.options.url, true);
	    xhr.withCredentials = !!this.options.withCredentials;
	    response = null;
	    handleError = (function(_this) {
		return function() {
		    var _j, _len1, _results;
		    _results = [];
		    for (_j = 0, _len1 = files.length; _j < _len1; _j++) {
			file = files[_j];
			_results.push(_this._errorProcessing(files, response || _this.options.dictResponseError.replace("{{statusCode}}", xhr.status), xhr));
		    }
		    return _results;
		};
	    })(this);
	    updateProgress = (function(_this) {
		return function(e) {
		    var allFilesFinished, progress, _j, _k, _l, _len1, _len2, _len3, _results;
		    if (e != null) {
			progress = 100 * e.loaded / e.total;
			for (_j = 0, _len1 = files.length; _j < _len1; _j++) {
			    file = files[_j];
			    file.upload = {
				progress : progress,
				total : e.total,
				bytesSent : e.loaded
			    };
			}
		    } else {
			allFilesFinished = true;
			progress = 100;
			for (_k = 0, _len2 = files.length; _k < _len2; _k++) {
			    file = files[_k];
			    if (!(file.upload.progress === 100 && file.upload.bytesSent === file.upload.total)) {
				allFilesFinished = false;
			    }
			    file.upload.progress = progress;
			    file.upload.bytesSent = file.upload.total;
			}
			if (allFilesFinished) {
			    return;
			}
		    }
		    _results = [];
		    for (_l = 0, _len3 = files.length; _l < _len3; _l++) {
			file = files[_l];
			_results.push(_this.emit("uploadprogress", file, progress, file.upload.bytesSent));
		    }
		    return _results;
		};
	    })(this);
	    xhr.onload = (function(_this) {
		return function(e) {
		    var _ref;
		    if (files[0].status === Dropzone.CANCELED) {
			return;
		    }
		    if (xhr.readyState !== 4) {
			return;
		    }
		    response = xhr.responseText;
		    if (xhr.getResponseHeader("content-type") && ~xhr.getResponseHeader("content-type").indexOf("application/json")) {
			try {
			    response = JSON.parse(response);
			} catch (_error) {
			    e = _error;
			    response = "Invalid JSON response from server.";
			}
		    }
		    updateProgress();
		    if (!((200 <= (_ref = xhr.status) && _ref < 300))) {
			return handleError();
		    } else {
			return _this._finished(files, response, e);
		    }
		};
	    })(this);
	    xhr.onerror = (function(_this) {
		return function() {
		    if (files[0].status === Dropzone.CANCELED) {
			return;
		    }
		    return handleError();
		};
	    })(this);
	    progressObj = (_ref = xhr.upload) != null ? _ref : xhr;
	    progressObj.onprogress = updateProgress;
	    headers = {
		"Accept" : "application/json",
		"Cache-Control" : "no-cache",
		"X-Requested-With" : "XMLHttpRequest"
	    };
	    if (this.options.headers) {
		$.extend(headers, this.options.headers);
	    }
	    for (headerName in headers) {
		headerValue = headers[headerName];
		xhr.setRequestHeader(headerName, headerValue);
	    }
	    formData = new FormData();
	    if (this.options.params) {
		_ref1 = this.options.params;
		for (key in _ref1) {
		    value = _ref1[key];
		    formData.append(key, value);
		}
	    }
	    for (_j = 0, _len1 = files.length; _j < _len1; _j++) {
		file = files[_j];
		this.emit("sending", file, xhr, formData);
	    }
	    if (this.options.uploadMultiple) {
		this.emit("sendingmultiple", files, xhr, formData);
	    }
	    if (this.element.tagName === "FORM") {
		_ref2 = this.element.querySelectorAll("input, textarea, select, button");
		for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
		    input = _ref2[_k];
		    inputName = input.getAttribute("name");
		    inputType = input.getAttribute("type");
		    if (input.tagName === "SELECT" && input.hasAttribute("multiple")) {
			_ref3 = input.options;
			for (_l = 0, _len3 = _ref3.length; _l < _len3; _l++) {
			    option = _ref3[_l];
			    if (option.selected) {
				formData.append(inputName, option.value);
			    }
			}
		    } else if (!inputType || ((_ref4 = inputType.toLowerCase()) !== "checkbox" && _ref4 !== "radio") || input.checked) {
			formData.append(inputName, input.value);
		    }
		}
	    }
	    for (_m = 0, _len4 = files.length; _m < _len4; _m++) {
		file = files[_m];
		formData.append("" + this.options.paramName + (this.options.uploadMultiple ? "[]" : ""), file, file.name);
	    }
	    return xhr.send(formData);
	},
	_finished : function(files, responseText, e) { // done
	    var self = this;
	    $.each(files, function(key, file) {
		file.status = Dropzone.SUCCESS;
		self.emit("success", file, responseText, e);
		self.emit("complete", file);
	    });
	    if (this.options.uploadMultiple) {
		this.emit("successmultiple", files, responseText, e);
		this.emit("completemultiple", files);
	    }
	    if (this.options.autoProcessQueue)
		return this.processQueue();
	},
	_errorProcessing : function(files, message, xhr) { // done
	    var self = this;
	    $.each(files, function(key, file) {
		file.status = Dropzone.ERROR;
		self.emit("error", file, message, xhr);
		self.emit("complete", file);
	    });
	    if (this.options.uploadMultiple) {
		this.emit("errormultiple", files, message, xhr);
		this.emit("completemultiple", files);
	    }
	    if (this.options.autoProcessQueue)
		return this.processQueue();
	}
    };

    $.fn.dropzone = function(options) {
	return this.each(function() {
	    new _Dropzone(this, options);
	});
    };

    $.extend(Dropzone, {
	version : "3.8.5",
	options : {},
	instances : [],
	createElement : function(string) {
	    var div;
	    div = document.createElement("div");
	    div.innerHTML = string;
	    return div.childNodes[0];
	},
	elementInside : function(element, container) {
	    if (element === container) {
		return true;
	    }
	    while (element = element.parentNode) {
		if (element === container) {
		    return true;
		}
	    }
	    return false;
	},
	getElement : function(el, name) {
	    var element;
	    if (typeof el === "string") {
		element = document.querySelector(el);
	    } else if (el.nodeType != null) {
		element = el;
	    }
	    if (element == null) {
		throw new Error("Invalid `" + name + "` option provided. Please provide a CSS selector or a plain HTML element.");
	    }
	    return element;
	},
	getElements : function(els, name) {
	    var e, el, elements, _i, _j, _len, _len1, _ref;
	    if (els instanceof Array) {
		elements = [];
		try {
		    for (_i = 0, _len = els.length; _i < _len; _i++) {
			el = els[_i];
			elements.push(this.getElement(el, name));
		    }
		} catch (_error) {
		    e = _error;
		    elements = null;
		}
	    } else if (typeof els === "string") {
		elements = [];
		_ref = document.querySelectorAll(els);
		for (_j = 0, _len1 = _ref.length; _j < _len1; _j++) {
		    el = _ref[_j];
		    elements.push(el);
		}
	    } else if (els.nodeType != null) {
		elements = [ els ];
	    }
	    if (!((elements != null) && elements.length)) {
		throw new Error("Invalid `" + name + "` option provided. Please provide a CSS selector, a plain HTML element or a list of those.");
	    }
	    return elements;
	},
	confirm : function(question, accepted, rejected) {
	    if (window.confirm(question)) {
		return accepted();
	    } else if (rejected != null) {
		return rejected();
	    }
	},
	isValidFile : function(file, acceptedFiles) {
	    var baseMimeType, mimeType, validType, _i, _len;
	    if (!acceptedFiles) {
		return true;
	    }
	    acceptedFiles = acceptedFiles.split(",");
	    mimeType = file.type;
	    baseMimeType = mimeType.replace(/\/.*$/, "");
	    for (_i = 0, _len = acceptedFiles.length; _i < _len; _i++) {
		validType = acceptedFiles[_i];
		validType = validType.trim();
		if (validType.charAt(0) === ".") {
		    if (file.name.toLowerCase().indexOf(validType.toLowerCase(), file.name.length - validType.length) !== -1) {
			return true;
		    }
		} else if (/\/\*$/.test(validType)) {
		    if (baseMimeType === validType.replace(/\/.*$/, "")) {
			return true;
		    }
		} else {
		    if (mimeType === validType) {
			return true;
		    }
		}
	    }
	    return false;
	},
	ADDED : "added",
	QUEUED : "queued",
	ACCEPTED : Dropzone.QUEUED,
	UPLOADING : "uploading",
	PROCESSING : Dropzone.UPLOADING,
	CANCELED : "canceled",
	ERROR : "error",
	SUCCESS : "success"
    });

    detectVerticalSquash = function(img) {
	var alpha, canvas, ctx, data, ey, ih, iw, py, ratio, sy;
	iw = img.naturalWidth;
	ih = img.naturalHeight;
	canvas = document.createElement("canvas");
	canvas.width = 1;
	canvas.height = ih;
	ctx = canvas.getContext("2d");
	ctx.drawImage(img, 0, 0);
	data = ctx.getImageData(0, 0, 1, ih).data;
	sy = 0;
	ey = ih;
	py = ih;
	while (py > sy) {
	    alpha = data[(py - 1) * 4 + 3];
	    if (alpha === 0) {
		ey = py;
	    } else {
		sy = py;
	    }
	    py = (ey + sy) >> 1;
	}
	ratio = py / ih;
	if (ratio === 0) {
	    return 1;
	} else {
	    return ratio;
	}
    };

    drawImageIOSFix = function(ctx, img, sx, sy, sw, sh, dx, dy, dw, dh) {
	var vertSquashRatio;
	vertSquashRatio = detectVerticalSquash(img);
	return ctx.drawImage(img, sx, sy, sw, sh, dx, dy, dw, dh / vertSquashRatio);
    };

})(jQuery);
