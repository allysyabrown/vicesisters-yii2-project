var Crop = {

    popupId: '#cropper-popup',
    fileInputId: '#inputImage',
    cropContainerId: '.crop-img-container',
    htMessageId: '.add-top-members-post-btn',

    cropDoneFunction: function(imageSource){},

    sendImageUrl: '',

    width: 150,
    height: 150,
    aspectRatio: 1,
    dataX: null,
    dataY: null,
    dataHeight: null,
    dataWidth: null,

    init: function(data){

        if(typeof data != 'undefined'){
            var attributes = [
                'popupId',
                'fileInputId',
                'cropContainerId',
                'htMessageId',
                'sendImageUrl',
                'width',
                'height',
                'aspectRatio',
                'dataX',
                'dataY',
                'dataHeight',
                'dataWidth',
                'hotMessageFormId',
                'hotMessageFileInput',
                'cropDoneFunction'
            ];

            $.each(attributes, function(index, element){
                if(typeof data[element] != 'undefined')
                    Crop[element] = data[element];
            });
        }

        Crop.setHandlers();
    },

    setHandlers: function(){
        Crop.popup();
    },


    // Handlers

    popup: function(){
        $(document).on('click', Crop.popupId, function(){
            //$('.shadow').show();
            $('.shadow.crop-ava').show();
            Crop.initCropper();
        });
    },

    // END Handlers


    // Private functions

    initCropper: function(){

        var imageInput = $(Crop.fileInputId);
        var image = $(Crop.cropContainerId+' img');
        var imageUploaded = false;

        var options = {
            aspectRatio: Crop.aspectRatio,
            zoomable: false,
                data: {
                x: 480,
                y: 60,
                width: 640,
                height: 360
            },
            preview: ".img-preview",
            done: function(data){
                /*$dataX.val(data.x);
                $dataY.val(data.y);
                $dataHeight.val(data.height);
                $dataWidth.val(data.width);*/
            }
        };

        if(window.FileReader){
            imageInput.on('change', function(){
                var fileReader = new FileReader(),
                    files = this.files,
                    file;
                if(!files.length){
                    return;
                }

                file = files[0];

                if(/^image\/\w+$/.test(file.type)){

                    image.cropper(options);
                    imageUploaded = true;

                    fileReader.readAsDataURL(file);
                    fileReader.onload = function () {
                        imageInput.val("");
                        image.cropper("reset", true).cropper("replace", this.result);
                        $(Crop.cropContainerId).fadeIn();
                    };
                }else{
                    console.log("Please choose an image file.");
                }
            });
        }else{
            imageInput.addClass("hide");
        }

        $('.back-btn').click(function(){
            $(Crop.cropContainerId).fadeOut();
        });

        $('.cropper-popup-wrap .done-btn').click(function(){
            if(imageUploaded){
                var imageSource = image.cropper("getDataURL", {width: Crop.width, height: Crop.height}, "image/jpeg", 1);
                Crop.cropDoneFunction(imageSource);
            }
        });
    },

    disable: function(callback){
        var img = $(Crop.cropContainerId+' img');

        img.cropper("destroy");
        img.cropper("disable");
        img.cropper("reset");
        img.cropper("clear");

        img.attr('src', ' ');
        $(Crop.fileInputId).val('');
        $('.img-preview').html(' ');

        if(typeof callback !== 'undefined'){
            callback();
        }
    }

    // END Private functions
}