import React, { memo, useEffect, useState, useMemo, useCallback } from "react";
import { CountSkeleton, PlansSkeleton } from "./PlansSkeleton";
import AlertUser from "../downloads/AlertUser";

const MembershipItem = memo(({ membership }) => (
  <div key={membership.id} className="p-4 rounded bg-gray-50 dark:bg-card">
    <h3 className="font-medium text-md">{membership.name}</h3>
    <p className="text-sm text-gray-500">Produto: {membership.product_name}</p>
    <p className="text-sm text-gray-500">Status: {membership.status}</p>
    <p className="text-sm text-gray-500">
      Início: {new Date(membership.start_date).toLocaleDateString()}
    </p>
  </div>
));

const SubscriptionItem = memo(({ subscription }) => (
  <div key={subscription.id} className="p-4 rounded bg-gray-50 dark:bg-card">
    <h3 className="font-medium text-md">{subscription.product_name}</h3>
    <p className="text-sm text-gray-500">Status: {subscription.status}</p>
    <p className="text-sm text-gray-500">
      Início: {new Date(subscription.start_date).toLocaleDateString()}
    </p>
    {subscription.next_payment_date !== 0 && (
      <p className="text-sm text-gray-500">
        Próximo Pagamento: {new Date(subscription.next_payment_date).toLocaleDateString()}
      </p>
    )}
  </div>
));

const MembershipList = memo(({ memberships }) => (
  <div className="space-y-4">
    {memberships.map((membership) => (
      <MembershipItem key={membership.id} membership={membership} />
    ))}
  </div>
));

const SubscriptionList = memo(({ subscriptions }) => (
  <div className="space-y-4">
    {subscriptions.map((subscription) => (
      <SubscriptionItem key={subscription.id} subscription={subscription} />
    ))}
  </div>
));

const Plans = memo(({ data, user }) => {
  
  
  const [isLoading, setIsLoading] = useState(!data);
  

  const activeMemberships = useMemo(() => 
    data ? data.membership.filter(membership => membership.status === "active") : []
  , [data]);

  const activeSubscriptions = useMemo(() => 
    data ? data.subscription.filter(subscription => subscription.status === "active") : []
  , [data]);

  useEffect(() => {
    if (data || user ) {
      setIsLoading(false);
    }
  }, [data, user]);

  const renderCount = useCallback((count) => (
    <p className="text-3xl font-bold">{count}</p>
  ), []);

  return (
    <div className="gap-4 rounded-lg">
      <div className="mb-2">
        { data === false  && <AlertUser />}
      </div>
      
      <h2 className="mb-4 text-lg font-medium">Resumo</h2>
      <div className="grid grid-cols-2 gap-4">
        <div className="p-4 rounded bg-gray-50 dark:bg-card">
          <h3 className="mb-2 font-medium text-md">Membros Ativos</h3>
          {isLoading ? <CountSkeleton /> 
          : user.status === "visitor" 
          ? '0'
          : renderCount(activeMemberships.length)}
        </div>
        <div className="p-4 rounded bg-gray-50 dark:bg-card">
          <h3 className="mb-2 font-medium text-md">Assinaturas Ativas</h3>
          {isLoading ? <CountSkeleton /> 
          : user.status === "visitor" 
          ? '0'
          : renderCount(activeSubscriptions.length)}
        </div>
      </div>

      <div className="grid grid-cols-2 gap-4 mt-4">
        <div className="p-4 rounded bg-gray-50 dark:bg-card">
          <h2 className="mt-4 mb-4 text-lg font-medium">Meus Planos de Membros</h2>
          
          {isLoading ? (
            <PlansSkeleton />
          ) : user.status === "visitor" ? (
            <p className="text-sm text-gray-500">Nenhum planos encontrado</p>
            ) : (
              <MembershipList memberships={data.membership} />
          )}
        </div>

        <div className="p-4 rounded bg-gray-50 dark:bg-card">
          <h2 className="mt-4 mb-4 text-lg font-medium">Minhas Assinaturas</h2>
          {isLoading ? (
            <PlansSkeleton />
          ) : user.status === "visitor" ? (
            <p className="text-sm text-gray-500">Nenhuma assinatura encontrada</p>
            ): (
            <SubscriptionList subscriptions={data.subscription} />
          )}
        </div>
      </div>
    </div>
  );
});

export default Plans;