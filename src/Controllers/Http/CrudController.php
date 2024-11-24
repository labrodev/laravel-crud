<?php

namespace Labrodev\Crud\Controllers\Http;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Labrodev\Crud\Exceptions\ActionMethodMissed;
use Labrodev\Crud\Exceptions\FormViewModelIncorrect;
use Labrodev\Crud\ViewModels\ControllerWithCrud;
use Labrodev\Domain\Actions\ModelCreateInterface;
use Labrodev\Domain\Actions\ModelRemoveInterface;
use Labrodev\Domain\Actions\ModelUpdateInterface;
use Labrodev\Domain\Data\DataInterface;
use Labrodev\Domain\Models\ModelInterface;
use Labrodev\Domain\Policies\GuardCrud;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;
use Spatie\QueryBuilder\QueryBuilder;

abstract class CrudController extends BaseController implements ControllerWithCrud
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @return mixed
     * @throws AuthorizationException
     * @throws FormViewModelIncorrect
     */
    public function create(): mixed
    {
        $this->authorize(GuardCrud::CREATE, $this->modelClass());

        $formViewModelClass = $this->formViewModelClass();

        $formViewModel = new $formViewModelClass();

        if (method_exists($formViewModel, 'view')) {
            return $formViewModel->view($this->formViewTemplate());
        } else {
            throw FormViewModelIncorrect::make($formViewModelClass);
        }
    }

    /**
     * @param QueryBuilder $query
     * @param array<string,mixed> $viewData
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     * @throws AuthorizationException
     */
    protected function performIndex(QueryBuilder $query, array $viewData = []): Application|Factory|View|\Illuminate\Foundation\Application
    {
        $this->authorize(GuardCrud::VIEW, $this->modelClass());

        $itemsPerPage = $this->fetchItemsPerPageValue();

        $items = $query->paginate($itemsPerPage);

        $this->shareItemsPerPageOptionsForView();
        $this->shareItemsPerPageValueForView($itemsPerPage);

        return view($this->indexTemplate(), array_merge(['items' => $items], $viewData));
    }

    /**
     * @param DataInterface $data
     * @return RedirectResponse
     * @throws ActionMethodMissed
     * @throws AuthorizationException
     */
    protected function performStore(DataInterface $data): RedirectResponse
    {
        $this->authorize(GuardCrud::CREATE, $this->modelClass());

        $actionCreate = $this->getActionCreate();
        $actionMethod = ModelCreateInterface::ACTION_NAME;

        if (!method_exists($actionCreate, $actionMethod)) {
            throw ActionMethodMissed::make(
                get_class($actionCreate),
                $actionMethod
            );
        }

        $model = $actionCreate->{$actionMethod}($data);

        return redirect()->action([$this->controllerClass(), ControllerWithCrud::EDIT_METHOD], $model->getRouteKeyName())
            ->with('success', trans('New item is added'));
    }

    /**
     * @param ModelInterface $model
     * @return mixed
     * @throws AuthorizationException
     * @throws FormViewModelIncorrect
     */
    protected function performEdit(ModelInterface $model): mixed
    {
        $this->authorize(GuardCrud::UPDATE, $model);

        $formViewModelClass = $this->formViewModelClass();

        $formViewModel = new $formViewModelClass($model);

        if (method_exists($formViewModel, 'view')) {
            return $formViewModel->view($this->formViewTemplate());
        } else {
            throw FormViewModelIncorrect::make($formViewModelClass);
        }
    }

    /**
     * @param ModelInterface $model
     * @param DataInterface $data
     * @return RedirectResponse
     * @throws ActionMethodMissed
     * @throws AuthorizationException
     */
    protected function performUpdate(ModelInterface $model, DataInterface $data): RedirectResponse
    {
        $this->authorize(GuardCrud::UPDATE, $model);

        $actionUpdate = $this->getActionUpdate();
        $actionMethod = ModelUpdateInterface::ACTION_NAME;

        if (!method_exists($actionUpdate, $actionMethod)) {
            throw ActionMethodMissed::make(
                get_class($actionUpdate),
                $actionMethod
            );
        }

        $actionUpdate->{$actionMethod}($model, $data);

        return redirect()->action([$this->controllerClass(), ControllerWithCrud::EDIT_METHOD], $model->{$model->getRouteKeyName()})
            ->with('success', trans('Item is updated'));
    }

    /**
     * @param ModelInterface $model
     * @return RedirectResponse
     * @throws AuthorizationException
     * @throws ActionMethodMissed
     */
    protected function performDestroy(ModelInterface $model): RedirectResponse
    {
        $this->authorize(GuardCrud::REMOVE, $model);

        $actionRemove = $this->getActionRemove();

        $actionMethod = ModelRemoveInterface::ACTION_NAME;

        $link = sprintf('%s?%s',
            action([$this->controllerClass(), ControllerWithCrud::INDEX_METHOD]),
            request()->getQueryString()
        );

        if (!method_exists($actionRemove, $actionMethod)) {
            throw ActionMethodMissed::make(
                get_class($actionRemove),
                $actionMethod
            );
        }

        $actionRemove->{$actionMethod}($model);

        return redirect($link)->with('success',
            trans('Item is removed')
        );
    }

    /**
     * @return ModelCreateInterface
     */
    abstract protected function getActionCreate(): ModelCreateInterface;

    /**
     * @return ModelUpdateInterface
     */
    abstract protected function getActionUpdate(): ModelUpdateInterface;

    /**
     * @return ModelRemoveInterface
     */
    abstract protected function getActionRemove(): ModelRemoveInterface;

    /**
     * @return string
     */
    abstract protected function indexTemplate(): string;

    /**
     * @return string
     */
    abstract protected function formViewTemplate(): string;

    /**
     * @return class-string
     */
    abstract protected function formViewModelClass(): string;

    /**
     * @return class-string
     */
    abstract protected function controllerClass(): string;

    /**
     * @return class-string
     */
    abstract protected function modelClass(): string;

    /**
     * @return array<int,int>
     */
    protected static function itemsPerPageOptions(): array
    {
        return [15,25,50,100];
    }

    /**
     * @return void
     */
    protected function shareItemsPerPageOptionsForView(): void
    {
        ViewFacade::share(ControllerWithCrud::ITEMS_PER_PAGE_OPTIONS_VARIABLE,
            self::itemsPerPageOptions()
        );
    }

    /**
     * @return integer
     */
    protected function fetchItemsPerPageValue(): int
    {
        $itemsPerPage = request()->input(ControllerWithCrud::ITEMS_PER_PAGE_QUERY_PARAMETER, ControllerWithCrud::DEFAULT_ITEMS_PER_PAGE);

        if (!in_array($itemsPerPage, self::itemsPerPageOptions())) {
            $itemsPerPage = ControllerWithCrud::DEFAULT_ITEMS_PER_PAGE;
        }

        if (is_numeric($itemsPerPage) && in_array((int)$itemsPerPage, self::itemsPerPageOptions(), true)) {
            return (int) $itemsPerPage;
        }

        return ControllerWithCrud::DEFAULT_ITEMS_PER_PAGE;
    }

    /**
     * @param integer $itemsPerPage
     * @return void
     */
    private function shareItemsPerPageValueForView(int $itemsPerPage): void
    {
        ViewFacade::share(ControllerWithCrud::ITEMS_PER_PAGE_VALUE_VARIABLE, $itemsPerPage);
    }
}
