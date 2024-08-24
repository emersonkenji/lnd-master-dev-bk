import React from 'react';
import { Skeleton } from '@/components/ui/skeleton';
import { Button } from '@/components/ui/button';

const CardSkeleton = () => {
  return (
    <div className="border rounded shadow card bg-card text-card-foreground">
      <div className="card-image">
        <Skeleton className="w-full h-64 bg-neutral-200 dark:bg-neutral-700" />

      </div>
      {/* <div className="ribbon ribbon-top-left">
        <Skeleton className="w-16 h-4 bg-neutral-200 dark:bg-neutral-700" />
      </div>
      <div className="ribbon ribbon-top-right">
        <Skeleton className="w-16 h-4 bg-neutral-200 dark:bg-neutral-700" />
      </div> */}
      <div className="card-header bg-slate-200 dark:bg-neutral-800">
        <div className="card-version card-d-flex">
          <Skeleton className="w-4 h-4 bg-neutral-200 dark:bg-neutral-700" />
          <Skeleton className="w-10 h-4 ml-2 bg-neutral-200 dark:bg-neutral-700" />
        </div>
        <div className="card-update card-d-flex">
          <Skeleton className="w-4 h-4 bg-neutral-200 dark:bg-neutral-700" />
          <Skeleton className="w-10 h-4 ml-2 bg-neutral-200 dark:bg-neutral-700" />
        </div>
        <div className="dow-count card-d-flex">
          <Skeleton className="w-4 h-4 bg-neutral-200 dark:bg-neutral-700" />
          <Skeleton className="w-10 h-4 ml-2 bg-neutral-200 dark:bg-neutral-700" />
        </div>
      </div>
      <div className="card-body">
        <Skeleton className="w-3/4 h-4 mb-2 bg-neutral-200 dark:bg-neutral-700" />
        <div className="card-description">
          <Skeleton className="w-1/4 h-3 mb-1 bg-neutral-200 dark:bg-neutral-700" />
          <Skeleton className="w-full h-4 bg-neutral-200 dark:bg-neutral-700" />
          <Skeleton className="w-full h-4 bg-neutral-200 dark:bg-neutral-700" />
          <Skeleton className="w-3/4 h-4 bg-neutral-200 dark:bg-neutral-700" />
        </div>
      </div>
      <div className="card-footer">
      <Skeleton className="w-1/5 bg-slate-200 dark:bg-neutral-700 h-9" />
      <Skeleton className="w-4/5 bg-neutral-200 dark:bg-neutral-700 h-9 " />
        
      </div>
    </div>
  );
};

export default CardSkeleton;
