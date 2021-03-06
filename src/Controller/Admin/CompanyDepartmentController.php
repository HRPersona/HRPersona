<?php

namespace Persona\Hris\Controller\Admin;

use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController;
use Persona\Hris\Component\Company\Model\CompanyDepartmentInterface;
use Persona\Hris\Component\Company\Model\CompanyInterface;
use Persona\Hris\DataTransformer\CompanyTransformer;
use Persona\Hris\Entity\CompanyDepartment;
use Persona\Hris\Repository\CompanyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @author Muhamad Surya Iksanudin <surya.iksanudin@personahris.com>
 *
 * @Route(path="/admin")
 */
class CompanyDepartmentController extends AdminController
{
    /**
     * @Route(path = "/company/department", name = "company_department")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function departmentPerCompanyAction(Request $request)
    {
        $company = $this->container->get(CompanyRepository::class)->find($request->query->get('id'));
        if (!$company) {
            throw new AccessDeniedHttpException();
        }

        $session = $this->get('session');
        $session->set('companyId', $company->getId());

        return $this->redirectToRoute('easyadmin', array(
            'action' => 'list',
            'sortField' => 'department',
            'sortDirection' => 'DESC',
            'entity' => 'CompanyDepartment',
        ));
    }

    /**
     * @return CompanyDepartmentInterface
     */
    protected function createNewEntity(): CompanyDepartmentInterface
    {
        if (!$companyId = $this->get('session')->get('companyId')) {
            throw new AccessDeniedHttpException();
        }

        $entity = new CompanyDepartment();
        $entity->setCompany($this->container->get(CompanyRepository::class)->find($companyId));

        return $entity;
    }

    /**
     * @param object $entity
     * @param string $view
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    protected function createEntityFormBuilder($entity, $view)
    {
        $builder = parent::createEntityFormBuilder($entity, $view);

        $company = $builder->get('company');
        $company->addModelTransformer($this->container->get(CompanyTransformer::class));
        /** @var CompanyInterface $companyEntity */
        if ($companyEntity = $entity->getCompany()) {
            $company->setData($companyEntity->getId());

            $builder->get('company_text')->setData($companyEntity);
        }

        return $builder;
    }

    /**
     * @param string $entityClass
     * @param string $searchQuery
     * @param array  $searchableFields
     * @param null   $sortField
     * @param null   $sortDirection
     * @param null   $dqlFilter
     *
     * @return QueryBuilder
     */
    protected function createSearchQueryBuilder($entityClass, $searchQuery, array $searchableFields, $sortField = null, $sortDirection = null, $dqlFilter = null)
    {
        return $this->container->get(CompanyRepository::class)->createCompanyDepartmentQueryBuilder($this->getDoctrine(), $searchQuery, $searchableFields, $sortField, $sortDirection, $dqlFilter);
    }
}
