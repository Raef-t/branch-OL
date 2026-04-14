// ignore_for_file: use_build_context_synchronously
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '/core/classes/selection_controller_class.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/core/styles/colors_style.dart';
import '/features/class/presentation/managers/models/batch_students_model.dart';
import '/features/class/presentation/view/widgets/custom_contain_card_about_student_in_class_view.dart';

class CustomCardAboutStudentInClassView extends StatelessWidget {
  const CustomCardAboutStudentInClassView({
    super.key,
    required this.batchStudentsModel,
    required this.index,
    required this.selectedIndex,
    required this.isVisible,
  });
  final BatchStudentsModel batchStudentsModel;
  final int index, selectedIndex;
  final bool isVisible;
  @override
  Widget build(BuildContext context) {
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return GestureDetector(
      onLongPress: () {
        Provider.of<SelectionControllerClass>(
          context,
          listen: false,
        ).enterSelectionMode();
        //listen: false, it's mean don't do rebuild when the controller(focus) when the controller are changes
      },
      onTap: () async {
        await StoreParametersInSharedPreferences.saveIntParameter(
          intValue: batchStudentsModel.id ?? 0,
          key: keyStudentIdInSharedPreferences,
        );
        pushGoRouterHelper(context: context, view: kDetailsStudentViewRouter);
      },
      child: Card(
        color: ColorsStyle.whiteColor,
        elevation: 0,
        margin: isRotait
            ? OnlyPaddingWithoutChild.left18AndRight22AndBottom8(
                context: context,
              )
            : OnlyPaddingWithoutChild.left18AndRight22AndBottom15(
                context: context,
              ),
        child: CustomContainCardAboutStudentInClassView(
          batchStudentsModel: batchStudentsModel,
          index: index,
          selectedIndex: selectedIndex,
          isVisible: isVisible,
        ),
      ),
    );
  }
}
