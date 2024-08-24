import React from "react";
import { Skeleton } from '@/components/ui/skeleton'; 

const TotalFiltersLoading = () => {
  return (
    <div className="inline-flex items-center justify-center gap-1 px-2 text-sm font-medium transition-colors rounded-md whitespace-nowrap focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9">
      <Skeleton className="w-32 h-5 bg-neutral-200 dark:bg-neutral-700" />
    </div>
  );
};

export default TotalFiltersLoading; 
