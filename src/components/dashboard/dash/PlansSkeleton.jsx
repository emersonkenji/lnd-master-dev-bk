import React from "react";
import { Skeleton } from "@/components/ui/skeleton";

const CountSkeleton = () => {
  return (
    <div className="text-3xl font-bold">
      <Skeleton className="w-8 h-8 bg-neutral-200 dark:bg-neutral-700" />
    </div>
  );
};

const PlansSkeleton = () => {
  return (
    [1, 2, 3].map((item) => (
      <div key={item} className="p-4 rounded bg-gray-50 dark:bg-card">
        <h3 className="font-medium text-md">
          <Skeleton className="w-4/5 h-4 mb-1 bg-neutral-200 dark:bg-neutral-700" />
        </h3>
        <div className="text-sm text-gray-500">
          <Skeleton className="w-1/4 h-3 mb-1 bg-neutral-200 dark:bg-neutral-700" />
        </div>
        <div className="text-sm text-gray-500">
          <Skeleton className="w-1/5 h-3 mb-1 bg-neutral-200 dark:bg-neutral-700" />
        </div>
        <div className="text-sm text-gray-500">
          <Skeleton className="w-1/2 h-3 mb-1 bg-neutral-200 dark:bg-neutral-700" />
        </div>
        <div className="text-sm text-gray-500">
          <Skeleton className="w-1/3 h-3 mb-1 bg-neutral-200 dark:bg-neutral-700" />
        </div>
      </div>
    ))
  );
};

const PlansSkeleton2 = () => {
  return (
    <div className="gap-4 p-4 rounded-lg shadow bg-gray-50 dark:bg-card">
      <h2 className="mb-4 text-lg font-medium">
        <Skeleton className="w-1/6 h-6 bg-neutral-200 dark:bg-neutral-700" />
      </h2>
      <div className="grid grid-cols-2 gap-4">
        <div className="p-4 rounded bg-gray-50 dark:bg-zinc-700">
          <h3 className="mb-2 font-medium text-md">
            <Skeleton className="w-1/2 h-4 bg-neutral-200 dark:bg-neutral-700" />
          </h3>
          <p className="text-3xl font-bold">
            <Skeleton className="w-1/3 h-8 bg-neutral-200 dark:bg-neutral-700" />
          </p>
        </div>
        <div className="p-4 rounded bg-gray-50 dark:bg-zinc-700">
          <h3 className="mb-2 font-medium text-md">
            <Skeleton className="w-1/2 h-4 bg-neutral-200 dark:bg-neutral-700" />
          </h3>
          <p className="text-3xl font-bold">
            <Skeleton className="w-1/3 h-8 bg-neutral-200 dark:bg-neutral-700" />
          </p>
        </div>
      </div>

      <div className="grid grid-cols-2 gap-4 mt-4">
        <div className="p-4 rounded bg-gray-50 dark:bg-zinc-700">
          <h2 className="mt-4 mb-4 text-lg font-medium">
            <Skeleton className="w-1/2 h-6 bg-neutral-200 dark:bg-neutral-700" />
          </h2>
          <div className="space-y-4">
            {[1, 2, 3].map((item) => (
              <div key={item} className="p-4 rounded bg-gray-50 dark:bg-card">
                <h3 className="font-medium text-md">
                  <Skeleton className="w-1/3 h-4 bg-neutral-200 dark:bg-neutral-700" />
                </h3>
                <p className="text-sm text-gray-500">
                  <Skeleton className="w-1/2 h-3 bg-neutral-200 dark:bg-neutral-700" />
                </p>
                <p className="text-sm text-gray-500">
                  <Skeleton className="w-1/2 h-3 bg-neutral-200 dark:bg-neutral-700" />
                </p>
                <p className="text-sm text-gray-500">
                  <Skeleton className="w-1/2 h-3 bg-neutral-200 dark:bg-neutral-700" />
                </p>
              </div>
            ))}
          </div>
        </div>

        <div className="p-4 rounded bg-gray-50 dark:bg-zinc-700">
          <h2 className="mt-4 mb-4 text-lg font-medium">
            <Skeleton className="w-1/2 h-6 bg-neutral-200 dark:bg-neutral-700" />
          </h2>
          <div className="space-y-4">
            {[1, 2, 3].map((item) => (
              <div key={item} className="p-4 rounded bg-gray-50 dark:bg-card">
                <h3 className="font-medium text-md">
                  <Skeleton className="w-1/3 h-4 bg-neutral-200 dark:bg-neutral-700" />
                </h3>
                <p className="text-sm text-gray-500">
                  <Skeleton className="w-1/2 h-3 bg-neutral-200 dark:bg-neutral-700" />
                </p>
                <p className="text-sm text-gray-500">
                  <Skeleton className="w-1/2 h-3 bg-neutral-200 dark:bg-neutral-700" />
                </p>
                {item === 1 && (
                  <p className="text-sm text-gray-500">
                    <Skeleton className="w-1/2 h-3 bg-neutral-200 dark:bg-neutral-700" />
                  </p>
                )}
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
};

export { PlansSkeleton, CountSkeleton };
