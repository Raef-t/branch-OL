import '/features/home/presentation/managers/models/batch_average/batch_average_model.dart';

abstract class BatchAveragesState {}

class BatchAveragesInitialState extends BatchAveragesState {}

class BatchAveragesLoadingState extends BatchAveragesState {}

class BatchAveragesSuccessState extends BatchAveragesState {
  final List<BatchAverageModel> listOfBatchAverageModelInCubit;
  BatchAveragesSuccessState({required this.listOfBatchAverageModelInCubit});
}

class BatchAveragesFailureState extends BatchAveragesState {
  final String errorMessageInCubit;
  BatchAveragesFailureState({required this.errorMessageInCubit});
}
