<h1 class="visually-hidden">Catégories</h1>

<p class="text-end"><a class="btn btn-primary" href="{$smarty.const.URL}/index.php/categories/edit/0">Ajouter une catégorie</a></p>

{include file="common/messages_session.tpl"}

{if $count_categories === 0}
    <p class="alert alert-info">Aucune catégorie.</p>
{else}

    <table class="caption-top table table-bordered table-condensed" summary="liste des categories">
        <caption class="text-center">Liste des catégories</caption>
        <thead>
            <tr class="categories-tr">
                <th class="col-md-2" id="head-order">Positionnement</th>
                <th class="col-md-4" id="head-name">Nom de la catégorie</th>
                <th class="col-md-3" id="head-action">Actions</th>
                <th class="col-md-3" id="head-note">Nombre de services</th>
            </tr>
        </thead>
        <tbody>
        {foreach $categories as $category}
        <tr>
            <td headers="head-order">
                <ul class="list-inline">
                {if $category->position > 1}
                <li class="list-inline-item">
                    <a class="btn" href="{$smarty.const.URL}/index.php/categories/up/{$category->id}"><i aria-hidden="true" class="bi bi-arrow-up-square-fill" title="Monter la catégorie"></i><span class="visually-hidden">Monter la catégorie</span></a>
                </li>
                {/if}

                {if $category->position < $count_categories}
                <li class="list-inline-item">
                    <a class="btn" href="{$smarty.const.URL}/index.php/categories/down/{$category->id}"><i aria-hidden="true" class="bi bi-arrow-down-square-fill" title="Descendre la catégorie"></i><span class="visually-hidden">Descendre la catégorie</span></a>
                </li>
                {/if}
                </ul>
            </td>
            <td headers="head-name">{$category->name}</td>
            <td headers="head-action">
                <ul class="list-inline">
                    <li class="list-inline-item"><a class="btn btn-sm btn-primary" href="{$smarty.const.URL}/index.php/categories/edit/{$category->id}">modifier</a></li>
                    <li class="list-inline-item"><a class="btn btn-sm btn-danger" href="{$smarty.const.URL}/index.php/categories/delete/{$category->id}">supprimer</a></li>
                </ul>
            </td>
            <td{if $category->count_services === 0} class="table-danger text-danger"{/if} headers="head-note">{$category->count_services} service{if $category->count_services > 1}s{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
{/if}
