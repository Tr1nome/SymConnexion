function addItemFormDeleteLink($itemFormLi){
    var $removeFormButton = $('<button type="button" class="btn-danger">Supprimer</button>');
    $itemFormLi.append($removeFormButton);
    $removeFormButton.on('click',function(e) {
        $itemFormLi.remove();
    });
}

function addItemForm($collectionHolder, $newItemLi){
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index',index +1);
    var $newFormLi = $('<div></div>').append(newForm);
    $newItemLi.before($newFormLi);
    addItemFormDeleteLink($newFormLi);


}


$(document).ready(function(){
    console.log("JQuery fonctionne");

    var $collectionHolder;
    var $addItemButton = $('<button type="button" class="add_item_link btn-success">Ajouter</button>');
    var $newItemLi = $('<div></div>').append($addItemButton);

    $collectionHolder = $('#task_list_listItems');
    $collectionHolder.append($newItemLi);

    $collectionHolder.find('.listitem').each(function(){
        addItemFormDeleteLink($(this));
    });
    $collectionHolder.data('index', $collectionHolder.find('.listitem').length);
    $addItemButton.on('click',function(){
        addItemForm($collectionHolder, $newItemLi);
    });



})