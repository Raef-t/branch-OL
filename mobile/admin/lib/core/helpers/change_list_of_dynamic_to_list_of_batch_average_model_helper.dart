import 'package:dio/dio.dart';
import '/features/home/presentation/managers/models/batch_average/batch_average_model.dart';

List<BatchAverageModel> changeListOfDynamicToListOfBatchAverageModelHelper({
  required Response<dynamic> response,
}) {
  final List<dynamic> listOfData = response.data['data'] as List;
  final List<BatchAverageModel> listOfBatchAverageModel = listOfData
      .map(
        (data) =>
            BatchAverageModel.fromJson(json: data as Map<String, dynamic>),
      )
      .toList();
  return listOfBatchAverageModel;
}
