import React from 'react';
import { Skeleton } from '@/components/ui/skeleton'; // Certifique-se de que este caminho está correto

const PaginationLoading = () => {
  return (
    <div className="flex space-x-1">
      <Skeleton className="w-20 h-9 bg-neutral-200 dark:bg-neutral-700" />
      <Skeleton className="w-9 h-9 bg-neutral-200 dark:bg-neutral-700" />
      <Skeleton className="w-9 h-9 bg-neutral-200 dark:bg-neutral-700" />
      <Skeleton className="w-9 h-9 bg-neutral-200 dark:bg-neutral-700" />
      <Skeleton className="w-9 h-9 bg-neutral-200 dark:bg-neutral-700" />
      <Skeleton className="w-9 h-9 bg-neutral-200 dark:bg-neutral-700" />
      <Skeleton className="w-9 h-9 bg-neutral-200 dark:bg-neutral-700" />
      <Skeleton className="w-20 h-9 bg-neutral-200 dark:bg-neutral-700" />
    </div>
  );
};

export default PaginationLoading;
