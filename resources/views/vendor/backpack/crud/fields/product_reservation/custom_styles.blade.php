<style>
    .disabled-select{
        background-color: #d5d5d5;
        opacity: 0.5;
        border-radius: 3px;
        cursor: not-allowed;
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
    }
    select[readonly].select2-hidden-accessible+.select2-container {
        pointer-events: none;
        touch-action: none;
    }
    select[readonly].select2-hidden-accessible+.select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }
    select[readonly].select2-hidden-accessible+.select2-container .select2-selection__arrow,
    select[readonly].select2-hidden-accessible+.select2-container .select2-selection__clear {
        display: none;
    }
    .existing-file a.file_clear_button.btn{
        pointer-events: none;
        touch-action: none;
    }
    #tab_preguntas-clave .form-group textarea{
        pointer-events: none;
        touch-action: none;
        background-color: #f8f9fa;
    }
</style>
