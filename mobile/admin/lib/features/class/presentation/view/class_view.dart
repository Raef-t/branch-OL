import 'dart:io';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:provider/provider.dart';
import '/core/classes/selection_controller_class.dart';
import '/core/components/child_and_bottom_navigation_bar_in_ios_component.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_and_bottom_navigation_bar_component.dart';
import '/features/class/presentation/view/widgets/custom_bottom_navigation_bar_in_class_view.dart';
import '/features/class/presentation/view/widgets/custom_class_view_body.dart';
import '/features/courses_details/presentation/managers/models/batches_courses_details_model.dart';

class ClassView extends StatelessWidget {
  const ClassView({super.key});

  @override
  Widget build(BuildContext context) {
    final batchesModel =
        GoRouterState.of(context).extra as BatchesCoursesDetailsModel;
    return ChangeNotifierProvider(
      create: (context) => SelectionControllerClass(),
      child: Platform.isIOS
          ? CupertinoPageScaffoldWithChildComponent(
              child: ChildAndBottomNavigationBarInIosComponent(
                widget: CustomClassViewBody(batchesModel: batchesModel),
                bottomNavigationBar:
                    const CustomBottomNavigationBarInClassView(),
              ),
            )
          : ScaffoldWithBodyAndBottomNavigationBarComponent(
              body: CustomClassViewBody(batchesModel: batchesModel),
              bottomNavigationBar: const CustomBottomNavigationBarInClassView(),
            ),
    );
  }
}
